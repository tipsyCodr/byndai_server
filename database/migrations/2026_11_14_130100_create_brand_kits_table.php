<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_kits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('business_name')->nullable();
            $table->string('business_category')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('primary_color', 20)->default('#1F51E5');
            $table->string('secondary_color', 20)->default('#25D366');
            $table->string('preferred_language', 50)->default('bilingual');
            $table->string('preferred_style', 50)->default('Modern');
            $table->string('preferred_aspect_ratio', 20)->default('1:1');
            $table->unsignedTinyInteger('footer_style')->default(0);
            $table->boolean('show_logo')->default(true);
            $table->boolean('show_name')->default(true);
            $table->boolean('show_contact')->default(true);
            $table->boolean('show_whatsapp')->default(true);
            $table->boolean('show_address')->default(true);
            $table->boolean('show_website')->default(true);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_kits');
    }
};

