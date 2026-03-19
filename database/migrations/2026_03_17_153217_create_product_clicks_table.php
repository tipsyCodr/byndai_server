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
        Schema::create('product_clicks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('viewer_id');
            $table->unsignedBigInteger('profile_id');
            $table->integer('click_count')->default(0);
            $table->timestamp('last_clicked_at')->nullable();
            $table->timestamps();

            // Optional Foreign Keys (recommended)
            $table->foreign('viewer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('profile_id')->references('id')->on('users')->onDelete('cascade');

            // Unique constraint (1 viewer → 1 profile entry)
            $table->unique(['viewer_id', 'profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_clicks');
    }
};
