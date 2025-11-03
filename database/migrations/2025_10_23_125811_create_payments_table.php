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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('currency', 50)->nullable();
            $table->string('payment_gateway', 50)->nullable();
            $table->string('payment_mode', 50)->nullable();
            $table->string('order_id', 100)->nullable();
            $table->integer('status')->default(0)->comment('0 is unpaid, 1 is paid');
            $table->json('response_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
