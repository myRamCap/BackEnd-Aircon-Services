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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id');
            $table->string('reference_number', 255)->nullable();
            $table->bigInteger('aircon_id');
            $table->bigInteger('services_id');
            $table->bigInteger('service_center_id');
            $table->string('status');
            $table->date('booking_date');
            $table->string('time');
            $table->string('tech_id', 255)->nullable();
            $table->text('notes')->nullable();
            $table->decimal('longitude', 19, 15)->nullable();
            $table->decimal('latitude', 19, 15)->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
