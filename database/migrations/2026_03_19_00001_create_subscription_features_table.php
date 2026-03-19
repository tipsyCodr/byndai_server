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
        Schema::create('user_subscription_features', function (Blueprint $table) {
            $table->id();

            // FK to user_subscriptions
            $table->unsignedBigInteger('user_subscription_id');

            // Feature details
            $table->string('feature'); // e.g. connections_per_day
            $table->string('limit_value'); // e.g. 50 / unlimited
            $table->integer('used_value')->default(0);

            // Optional unit
            $table->string('unit')->nullable(); // e.g. requests, leads

            // Reset logic
            $table->enum('reset_period', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->date('last_reset_date')->nullable();

            $table->timestamps();

            // Foreign Key
            $table->foreign('user_subscription_id')
                  ->references('id')
                  ->on('user_subscriptions')
                  ->onDelete('cascade');

            // Index for fast lookup
            $table->index(['user_subscription_id', 'feature']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscription_features');
    }
};