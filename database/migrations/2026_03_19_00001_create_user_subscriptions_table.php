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
    // Old table delete karo agar exist karta hai
    if (Schema::hasTable('subscriptions')) {
        Schema::drop('subscriptions');
    }

    // New table create karo
    Schema::create('user_subscriptions', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->foreignId('plan_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->enum('payment_status', ['pending', 'paid', 'failed'])
              ->default('pending');

        $table->string('payment_id')->nullable();

        $table->enum('payment_method', ['online', 'offline'])
              ->default('online');

        $table->date('start_date');
        $table->date('end_date');

        $table->boolean('is_active')->default(true);

        $table->timestamps();

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