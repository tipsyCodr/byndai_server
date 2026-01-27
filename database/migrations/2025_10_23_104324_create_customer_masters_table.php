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
        Schema::create('customer_masters', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 50)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('desg',50)->nullable();
            $table->string('mobile_no', 15)->nullable();
            $table->string('website', 150)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('address', 100)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('date')->nullable();
            $table->string('front_image_url', 150)->nullable();
            $table->string('back_image_url', 150)->nullable();
            $table->unsignedBigInteger('assign_to')->nullable();
            $table->text('notes')->nullable();
            $table->integer('source')->default(0)->comment('0 is web, 1 is app');
            $table->integer('lead_status')->default(1)->comment('0 is in-active or 1 is active');
            $table->integer('cat_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_masters');
    }
};
