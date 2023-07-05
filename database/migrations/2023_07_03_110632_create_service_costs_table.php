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
        Schema::create('service_costs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('service_center_id');
            $table->bigInteger('service_id');
            $table->bigInteger('cost');
            $table->bigInteger('markup');
            $table->bigInteger('price');
            $table->longText('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_costs');
    }
};
