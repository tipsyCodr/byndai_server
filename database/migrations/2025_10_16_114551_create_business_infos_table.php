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
        Schema::create('business_infos', function (Blueprint $table) {
            $table->id();
            $table->string('legal_name')->nullable();
            $table->string('category')->nullable();
            $table->string('gst_no')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('description')->nullable();
            $table->string('bank_acc_no')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('website_url')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('upi_id')->nullable();
            $table->string('payment_qr')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_infos');
    }
};
