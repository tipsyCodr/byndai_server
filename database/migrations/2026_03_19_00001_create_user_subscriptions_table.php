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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('plan_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Payment details
            $table->enum('payment_status', ['pending', 'paid', 'failed'])
                  ->default('pending');

            $table->string('payment_id')->nullable();

            $table->enum('payment_method', ['online', 'offline'])
                  ->default('online');

            // Subscription dates
            $table->date('start_date');
            $table->date('end_date');

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Optional: Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index('plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};