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
        Schema::table('master_user_details', function (Blueprint $table) {
            $table->boolean('jci_member')->default(false)->after('profile_banner');
            $table->boolean('bni_member')->default(false)->after('jci_member');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_user_details', function (Blueprint $table) {
            $table->dropColumn(['jci_member', 'bni_member']);
        });
    }
};
