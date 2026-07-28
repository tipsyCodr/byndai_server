<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('referrers')->onDelete('cascade');
            $table->foreignId('coupon_id')->constrained('referral_coupons')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Customer
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->decimal('original_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('final_amount', 10, 2);
            $table->string('payment_status')->default('paid');
            $table->decimal('commission_amount', 10, 2)->default(0.00);
            $table->softDeletes();
            $table->timestamps();

            $table->index('referrer_id');
            $table->index('user_id');
            $table->index('coupon_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_sales');
    }
};
