<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('master_links') && ! Schema::hasColumn('master_links', 'user_id')) {
            Schema::table('master_links', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('title');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('master_links') && Schema::hasColumn('master_links', 'user_id')) {
            Schema::table('master_links', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
};
