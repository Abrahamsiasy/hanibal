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
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('betting_option_id')->constrained()->cascadeOnDelete();
            $table->decimal('stake', 12, 2);
            $table->decimal('odds', 8, 2);
            $table->decimal('potential_payout', 12, 2);
            $table->string('status')->default('pending');
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['city_event_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};
