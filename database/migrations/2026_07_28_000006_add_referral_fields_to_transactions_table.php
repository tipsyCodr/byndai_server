<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->constrained('referral_coupons')->onDelete('set null');
            $table->foreignId('referrer_id')->nullable()->constrained('referrers')->onDelete('set null');
            $table->decimal('discount_amount', 10, 2)->default(0.00);
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropForeign(['referrer_id']);
            $table->dropColumn(['coupon_id', 'referrer_id', 'discount_amount']);
        });
    }
};
