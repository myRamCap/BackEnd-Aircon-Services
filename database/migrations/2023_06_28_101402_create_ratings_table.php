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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->string('service_center_id')->nullable();
            $table->string('booking_id')->nullable();
            $table->string('tech_ref_id')->nullable();
            $table->integer('quality_of_service')->nullable();
            $table->integer('quick_service')->nullable();
            $table->integer('general_exp')->nullable();
            $table->longText('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
