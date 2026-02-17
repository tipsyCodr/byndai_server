<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Posts Table
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('title', 200)->nullable();
                $table->text('description')->nullable();
                $table->integer('industry_id');
                $table->enum('visibility', ['public', 'friends', 'private'])->default('public');
                $table->enum('status', ['active', 'deleted'])->default('active');
                $table->unsignedInteger('like_count')->default(0);
                $table->unsignedInteger('dislike_count')->default(0);
                $table->unsignedInteger('comment_count')->default(0);
                $table->unsignedInteger('share_count')->default(0);
                $table->unsignedInteger('save_count')->default(0);
                $table->unsignedInteger('view_count')->default(0);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Post Media Table
        if (!Schema::hasTable('post_media')) {
            Schema::create('post_media', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->enum('media_type', ['image', 'video']);
                $table->text('media_url');
                $table->text('thumbnail_url')->nullable();
                $table->unsignedInteger('media_order')->default(0);
                $table->integer('position');
                $table->timestamps();

                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            });
        }

        // Post Comments Table
        if (!Schema::hasTable('post_comments')) {
            Schema::create('post_comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->text('comment');
                $table->enum('status', ['active', 'deleted'])->default('active');
                $table->timestamps();

                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('parent_id')->references('id')->on('post_comments')->onDelete('cascade');
            });
        }

        // Post Reactions Table
        if (!Schema::hasTable('post_reactions')) {
            Schema::create('post_reactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id');
                $table->enum('reaction_type', ['like', 'dislike'])->default('like');
                $table->enum('reaction', ['like', 'dislike']);
                $table->timestamps();

                $table->unique(['post_id', 'user_id']);
                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Post Views Table
        if (!Schema::hasTable('post_views')) {
            Schema::create('post_views', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // Post Saves Table
        if (!Schema::hasTable('post_saves')) {
            Schema::create('post_saves', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                $table->unique(['post_id', 'user_id']);
                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Post Shares Table
        if (!Schema::hasTable('post_shares')) {
            Schema::create('post_shares', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id');
                $table->enum('share_type', ['internal', 'external', 'whatsapp', 'facebook', 'instagram', 'telegram', 'copy_link'])->default('external');
                $table->timestamps();

                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Post Reports Table
        if (!Schema::hasTable('post_reports')) {
            Schema::create('post_reports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id');
                $table->string('reason', 255);
                $table->string('message', 250);
                $table->timestamps();

                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_reports');
        Schema::dropIfExists('post_shares');
        Schema::dropIfExists('post_saves');
        Schema::dropIfExists('post_views');
        Schema::dropIfExists('post_reactions');
        Schema::dropIfExists('post_comments');
        Schema::dropIfExists('post_media');
        Schema::dropIfExists('posts');
    }
};
