<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('referrers')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable(); // bank_transfer, upi, cash
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('referrer_id');
        });

        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_sale_id')->constrained('referral_sales')->onDelete('cascade');
            $table->foreignId('referrer_id')->constrained('referrers')->onDelete('cascade');
            $table->foreignId('payout_id')->nullable()->constrained('commission_payouts')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('referrer_id');
            $table->index('referral_sale_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_commissions');
        Schema::dropIfExists('commission_payouts');
    }
};
