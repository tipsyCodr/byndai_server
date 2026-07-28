<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default referral settings
        DB::table('referral_settings')->insert([
            ['key' => 'auto_approve_referrers', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'default_discount_type', 'value' => 'percentage', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'default_discount_value', 'value' => '10.00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'default_commission_type', 'value' => 'percentage', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'default_commission_value', 'value' => '10.00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_settings');
    }
};
