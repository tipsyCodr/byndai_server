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
        if (Schema::hasTable('master_event_images') && !Schema::hasColumn('master_event_images', 'event_img_path')) {
            Schema::table('master_event_images', function (Blueprint $table) {
                $table->string('event_img_path', 250)->nullable()->after('event_img');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('master_event_images') && Schema::hasColumn('master_event_images', 'event_img_path')) {
            Schema::table('master_event_images', function (Blueprint $table) {
                $table->dropColumn('event_img_path');
            });
        }
    }
};
