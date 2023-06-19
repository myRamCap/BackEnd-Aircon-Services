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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('corporate_account_id');
            $table->string('category');
            $table->json('client')->nullable();
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
        Schema::dropIfExists('promotions');
    }
};
