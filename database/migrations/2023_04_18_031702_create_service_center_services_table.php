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
        Schema::create('service_center_services', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('service_center_id');
            $table->bigInteger('service_id');
            $table->time('estimated_time');
            $table->string('estimated_time_desc');
            $table->decimal('price', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_center_services');
    }
};
