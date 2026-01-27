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
        Schema::create('master_post_images', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('post_id')->nullable();
            $table->string('post_img')->nullable();
            $table->integer('type')->default(0)->comment('0 is image or 1 is video');
            $table->tinyInteger('status')->default(1)->comment('0 is inactive, 1 is active');
            $table->integer('position')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_post_images');
    }
};
