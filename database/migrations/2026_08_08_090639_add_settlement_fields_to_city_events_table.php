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
        Schema::table('city_events', function (Blueprint $table) {
            $table->foreignId('winning_betting_option_id')
                ->nullable()
                ->after('active')
                ->constrained('betting_options')
                ->nullOnDelete();
            $table->timestamp('settled_at')->nullable()->after('winning_betting_option_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('city_events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('winning_betting_option_id');
            $table->dropColumn('settled_at');
        });
    }
};
