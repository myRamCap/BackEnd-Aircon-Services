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
        Schema::create('tech_infos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('service_center_id');
            $table->bigInteger('tech_id');
            $table->string('tech_ref_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tech_infos');
    }
};
