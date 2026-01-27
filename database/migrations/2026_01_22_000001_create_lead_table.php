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
        Schema::create('leads', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('company_name')->nullable();
        $table->string('email')->nullable();
        $table->string('address')->nullable();

        $table->string('mobile_no_1', 15);
        $table->string('mobile_no_2', 15)->nullable();
        $table->string('mobile_no_3', 15)->nullable();

        $table->timestamps(); // created_at + updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
