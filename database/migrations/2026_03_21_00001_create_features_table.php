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
        if (!Schema::hasTable('features')) {
            Schema::create('features', function (Blueprint $table) {
                $table->id();

                // 🔑 unique feature key
                $table->string('feature')->unique();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('features')) {
            Schema::dropIfExists('features');
        }
    }
};