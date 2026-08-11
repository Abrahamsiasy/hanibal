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
        Schema::create('city_banners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedTinyInteger('position')->default(0);
            $table->timestamps();

            $table->index(['city_id', 'active', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('city_banners');
    }
};
