<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->ensureFeatureIdColumns();
        $this->backfillFeatureIds();
        $this->relaxLegacyFeatureColumns();

        $this->replaceForeignKey('plan_features', 'plan_id', 'subscription_plans', 'id', 'cascade');
        $this->replaceForeignKey('user_subscriptions', 'plan_id', 'subscription_plans', 'id', 'cascade');

        $this->addForeignKeyIfColumnExists('plan_features', 'feature_id', 'features', 'id', 'cascade');
        $this->addForeignKeyIfColumnExists('user_subscription_features', 'feature_id', 'features', 'id', 'cascade');

        $this->addForeignKeyIfColumnExists('transactions', 'user_id', 'users', 'id', 'set null');
        $this->addForeignKeyIfColumnExists('transactions', 'plan_id', 'subscription_plans', 'id', 'set null');
        $this->addForeignKeyIfColumnExists('transactions', 'subscription_id', 'user_subscriptions', 'id', 'set null');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropForeignKeysForColumn('transactions', 'subscription_id');
        $this->dropForeignKeysForColumn('transactions', 'plan_id');
        $this->dropForeignKeysForColumn('transactions', 'user_id');
        $this->dropForeignKeysForColumn('user_subscription_features', 'feature_id');
        $this->dropForeignKeysForColumn('plan_features', 'feature_id');
        $this->dropForeignKeysForColumn('user_subscriptions', 'plan_id');
        $this->dropForeignKeysForColumn('plan_features', 'plan_id');
    }

    private function ensureFeatureIdColumns(): void
    {
        if (Schema::hasTable('plan_features') && !Schema::hasColumn('plan_features', 'feature_id')) {
            Schema::table('plan_features', function (Blueprint $table) {
                $table->unsignedBigInteger('feature_id')->nullable()->after('plan_id');
            });
        }

        if (Schema::hasTable('user_subscription_features') && !Schema::hasColumn('user_subscription_features', 'feature_id')) {
            Schema::table('user_subscription_features', function (Blueprint $table) {
                $table->unsignedBigInteger('feature_id')->nullable()->after('user_subscription_id');
            });
        }
    }

    private function backfillFeatureIds(): void
    {
        if (
            Schema::hasTable('plan_features')
            && Schema::hasColumn('plan_features', 'feature')
            && Schema::hasColumn('plan_features', 'feature_id')
        ) {
            DB::statement(
                'UPDATE plan_features pf
                 INNER JOIN features f ON f.feature = pf.feature
                 SET pf.feature_id = f.id
                 WHERE pf.feature_id IS NULL'
            );
        }

        if (
            Schema::hasTable('user_subscription_features')
            && Schema::hasColumn('user_subscription_features', 'feature')
            && Schema::hasColumn('user_subscription_features', 'feature_id')
        ) {
            DB::statement(
                'UPDATE user_subscription_features usf
                 INNER JOIN features f ON f.feature = usf.feature
                 SET usf.feature_id = f.id
                 WHERE usf.feature_id IS NULL'
            );
        }
    }

    private function relaxLegacyFeatureColumns(): void
    {
        if (Schema::hasTable('plan_features') && Schema::hasColumn('plan_features', 'feature')) {
            DB::statement('ALTER TABLE plan_features MODIFY feature VARCHAR(255) NULL');
        }

        if (Schema::hasTable('user_subscription_features') && Schema::hasColumn('user_subscription_features', 'feature')) {
            DB::statement('ALTER TABLE user_subscription_features MODIFY feature VARCHAR(255) NULL');
        }

        if (Schema::hasTable('user_subscription_features') && Schema::hasColumn('user_subscription_features', 'limit_value')) {
            DB::statement('ALTER TABLE user_subscription_features MODIFY limit_value VARCHAR(255) NULL');
        }
    }

    private function addForeignKeyIfColumnExists(
        string $table,
        string $column,
        string $referencesTable,
        string $referencesColumn = 'id',
        string $onDelete = 'cascade'
    ): void {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column) || !Schema::hasTable($referencesTable)) {
            return;
        }

        $this->replaceForeignKey($table, $column, $referencesTable, $referencesColumn, $onDelete);
    }

    private function replaceForeignKey(
        string $table,
        string $column,
        string $referencesTable,
        string $referencesColumn = 'id',
        string $onDelete = 'cascade'
    ): void {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column) || !Schema::hasTable($referencesTable)) {
            return;
        }

        $this->dropForeignKeysForColumn($table, $column);
        $this->assertNoOrphans($table, $column, $referencesTable, $referencesColumn);

        Schema::table($table, function (Blueprint $tableBlueprint) use ($column, $referencesTable, $referencesColumn, $onDelete) {
            $foreign = $tableBlueprint->foreign($column)
                ->references($referencesColumn)
                ->on($referencesTable);

            match ($onDelete) {
                'cascade' => $foreign->cascadeOnDelete(),
                'set null' => $foreign->nullOnDelete(),
                default => null,
            };
        });
    }

    private function dropForeignKeysForColumn(string $table, string $column): void
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return;
        }

        $foreignKeys = DB::select(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$table, $column]
        );

        foreach ($foreignKeys as $foreignKey) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($foreignKey) {
                $tableBlueprint->dropForeign($foreignKey->CONSTRAINT_NAME);
            });
        }
    }

    private function assertNoOrphans(
        string $table,
        string $column,
        string $referencesTable,
        string $referencesColumn
    ): void {
        $orphans = DB::select(
            "SELECT child.{$column}
             FROM {$table} child
             LEFT JOIN {$referencesTable} parent ON parent.{$referencesColumn} = child.{$column}
             WHERE child.{$column} IS NOT NULL
               AND parent.{$referencesColumn} IS NULL
             GROUP BY child.{$column}
             LIMIT 10"
        );

        if ($orphans === []) {
            return;
        }

        $values = collect($orphans)
            ->pluck($column)
            ->implode(', ');

        throw new RuntimeException(
            "Cannot add foreign key {$table}.{$column} -> {$referencesTable}.{$referencesColumn}; orphan values found: {$values}"
        );
    }
};
