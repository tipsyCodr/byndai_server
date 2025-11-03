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
        Schema::create('master_user_details', function (Blueprint $table) {
            $table->bigIncrements('emp_id');
            $table->string('fname')->nullable();
            $table->unsignedBigInteger('category')->nullable();
            $table->string('dept',50)->nullable();
            $table->string('desg',50)->nullable();
            $table->string('mobile_one', 15)->unique()->nullable();
            $table->string('mobile_two', 15)->unique()->nullable();
            $table->string('email_one')->unique()->nullable();
            $table->string('email_two')->unique()->nullable();
            $table->string('user_id')->nullable();
            $table->string('imei_no')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['m', 'f'])->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->text('address')->nullable();
            $table->string('busi_type')->nullable();
            $table->json('social_links')->nullable();
            $table->string('profile_pic')->nullable();
            $table->string('profile_video')->nullable();
            $table->string('profile_logo')->nullable();
            $table->string('profile_banner')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_user_details');
    }
};
