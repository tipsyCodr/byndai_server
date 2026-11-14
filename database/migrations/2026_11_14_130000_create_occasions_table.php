<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('occasions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('date', 10)->comment('Format MM-DD');
            $table->unsignedSmallInteger('year')->nullable()->comment('Null means recurring every year');
            $table->string('region', 100)->default('National');
            $table->string('language', 50)->default('bilingual');
            $table->boolean('is_active')->default(true);
            $table->text('sample_prompts')->nullable();
            $table->timestamps();

            $table->index(['date', 'is_active']);
            $table->index('region');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('occasions');
    }
};

