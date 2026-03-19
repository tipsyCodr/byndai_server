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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('slug', 100)->unique();

            $table->text('description')->nullable();

            $table->enum('type', [
                'daily',
                'weekly',
                'monthly',
                'quarterly',
                'yearly'
            ]);

            $table->boolean('is_free')->default(false);

            $table->decimal('price', 10, 2)->default(0);

            $table->string('currency', 10)->default('INR');

            $table->tinyInteger('status')->default(1); // 1=active, 0=inactive

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};