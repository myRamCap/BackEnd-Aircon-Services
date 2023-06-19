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
        Schema::create('service_centers', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->nullable();
            $table->string('name');
            $table->string('category');
            $table->string('country');
            $table->string('house_number');
            $table->string('barangay');
            $table->string('municipality');
            $table->string('province');
            $table->decimal('longitude', 19, 15);
            $table->decimal('latitude', 19, 15);
            $table->integer('facility');
            $table->string('corporate_manager_id');
            $table->longText('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_centers');
    }
};
