<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profile_link_clicks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('viewer_id');
            $table->unsignedBigInteger('profile_id');

            // youtube, instagram, facebook etc
            $table->string('platform', 50);

            $table->unsignedInteger('click_count')->default(0);
            $table->timestamp('last_clicked_at')->nullable();

            $table->timestamps();

            $table->unique(['viewer_id', 'profile_id', 'platform']);

            $table->foreign('viewer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('profile_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_link_clicks');
    }
};
