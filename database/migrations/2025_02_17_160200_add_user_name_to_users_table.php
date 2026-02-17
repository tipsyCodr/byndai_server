<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'user_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_name', 255)->nullable()->after('id');
            });

            // Populate existing users with a unique user_name
            \Illuminate\Support\Facades\DB::table('users')->whereNull('user_name')->orWhere('user_name', '')->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    \Illuminate\Support\Facades\DB::table('users')
                        ->where('id', $user->id)
                        ->update(['user_name' => 'user_' . $user->id]);
                }
            });

            // Now add the unique constraint and make it not nullable
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_name', 255)->nullable(false)->unique()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'user_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_name');
            });
        }
    }
};
