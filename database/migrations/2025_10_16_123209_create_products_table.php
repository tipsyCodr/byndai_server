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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('product_name', 150)->nullable();
            $table->decimal('product_price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('sku')->nullable();
            $table->string('tags')->nullable();
            $table->integer('stock')->nullable();
            $table->boolean('status')->default(1)->comment('0 is in-active or 1 is active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
