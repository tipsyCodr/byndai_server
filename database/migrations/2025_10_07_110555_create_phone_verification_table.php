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
        Schema::create('phone_verification', function (Blueprint $table) {
            $table->id();
            $table->string('user_mobile', 12)->unique();
            $table->string('otp', 10)->nullable();
            $table->string('token', 150)->nullable();
            $table->unsignedTinyInteger('no_of_attempt')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_verification');
    }
};
