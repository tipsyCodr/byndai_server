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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('plan_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('trial_end_date')->nullable();
            $table->integer('status')->default(0)->comment('0 is in-active 1 is active 2 is pending 3 is trial');
            $table->unsignedBigInteger('payment_id');
            $table->integer('renewal_status')->default(10)->comment('10 is new plan 9 is renewal plan');
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
