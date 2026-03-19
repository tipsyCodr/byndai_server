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
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plan_id')
                  ->constrained('plans')
                  ->cascadeOnDelete();

            $table->string('feature'); // e.g. connections_per_day

            $table->string('value'); // e.g. 50, unlimited

            $table->string('unit', 50)->nullable(); // e.g. requests, leads

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};