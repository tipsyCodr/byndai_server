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
        Schema::create('master_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indus_type');
            $table->string('title', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('link', 255)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->time('timing')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->string('location', 255)->nullable();
            $table->string('event_type', 100)->nullable();
            $table->integer('max_attendees')->nullable();
            $table->tinyInteger('is_audience')->default(0)->comment('0 is public or 1 is connection');
            $table->string('is_online')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 is in-active or 1 is active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_events');
    }
};
