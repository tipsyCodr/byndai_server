<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flyer_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 50)->default('special')->comment('occasion, special, custom');
            $table->string('category', 100);
            $table->foreignId('occasion_id')->nullable()->constrained('occasions')->nullOnDelete();
            $table->text('prompt');
            $table->string('headline')->nullable();
            $table->text('subheadline')->nullable();
            $table->string('language', 30)->default('en');
            $table->string('design_style', 50)->default('Modern');
            $table->string('aspect_ratio', 20)->default('1:1');
            $table->string('image_path')->nullable()->comment('Composed final branded flyer image');
            $table->string('raw_ai_image_path')->nullable()->comment('Raw background image returned by AI');
            $table->string('status', 30)->default('pending')->comment('pending, processing, completed, failed');
            $table->text('error_message')->nullable();
            $table->json('branding_options')->nullable();
            $table->json('generation_metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flyer_generations');
    }
};

