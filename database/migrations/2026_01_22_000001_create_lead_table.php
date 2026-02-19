<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {

            $table->id();

            // ✅ Login user id
            $table->unsignedBigInteger('user_id')->nullable();

            // ✅ Category
            $table->string('category')->nullable();

            $table->string('name');
            $table->string('company_name')->nullable();

            $table->string('email')->nullable();
            $table->string('email2')->nullable();

            $table->string('website')->nullable();
            $table->string('address')->nullable();

            // ✅ Mobiles
            $table->string('mobile_no_1', 15);
            $table->string('mobile_no_2', 15)->nullable();
            $table->string('mobile_no_3', 15)->nullable();

            // ✅ Lead Card Image (scanned)
            $table->string('lead_image')->nullable();
            $table->string('image_filename')->nullable();

            // ✅ Selfy Images (max 2)
            $table->string('selfy_image_1')->nullable();
            $table->string('selfy_image_2')->nullable();

            // ✅ Video (max 10 sec)
            $table->string('selfy_video')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
