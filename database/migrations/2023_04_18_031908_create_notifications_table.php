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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('corporate_account_id');
            $table->string('category');
            // $table->bigInteger('corporate_id')->nullable();
            // $table->bigInteger('service_center_id')->nullable();
            $table->json('service_center')->nullable();
            $table->date('datefrom');
            $table->date('dateto');
            $table->string('title')->nullable();
            $table->string('content')->nullable();
            $table->longText('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
