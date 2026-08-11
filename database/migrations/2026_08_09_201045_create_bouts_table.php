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
        Schema::create('bouts', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('sport');
            $table->unsignedSmallInteger('bout_number');
            $table->boolean('is_main_event')->default(false);
            $table->unsignedSmallInteger('rounds')->nullable();
            $table->string('weight_class')->nullable();
            $table->unsignedBigInteger('red_fighter_id');
            $table->unsignedBigInteger('blue_fighter_id');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->timestamps();

            $table->foreign('red_fighter_id')->references('id')->on('fighters');
            $table->foreign('blue_fighter_id')->references('id')->on('fighters');
            $table->foreign('event_id')->references('id')->on('events');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bouts');
    }
};
