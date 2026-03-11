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
        if (!Schema::hasTable('user_reviews')) {
            Schema::create('user_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->tinyInteger('rating')->unsigned()->default(5); // 1-5
                $table->text('message')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reviews');
    }
};
