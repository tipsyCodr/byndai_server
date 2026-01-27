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
        Schema::create('master_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indus_type');
            $table->string('title', 100)->nullable();
            $table->string('slug')->nullable();
            $table->text('content');
            $table->tinyInteger('is_audience')->default(0)->comment('0 is public or 1 is connection');
            $table->tinyInteger('status')->default(0)->comment('0 is in-active or 1 is active');
            $table->unsignedBigInteger('like')->default(0);
            $table->unsignedBigInteger('comments')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_posts');
    }
};
