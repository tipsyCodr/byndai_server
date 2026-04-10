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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'bni_id_card_image')) {
                $table->string('bni_id_card_image')->nullable()->after('last_login_ip');
            }
            if (! Schema::hasColumn('users', 'jci_id_card_image')) {
                $table->string('jci_id_card_image')->nullable()->after('bni_id_card_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bni_id_card_image', 'jci_id_card_image']);
        });
    }
};
