<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lmu_laps', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('lmu_session_participation_id')->constrained()->onDelete('cascade');
            $table->foreignId('lmu_compound_id')->constrained()->onDelete('cascade');
            $table->integer('lap_number')->nullable(false);
            $table->integer('finish_position')->nullable(false);
            $table->float('lap_time')->nullable(false);
            $table->float('top_speed')->nullable(false);
            $table->float('remaining_fuel')->nullable(false);
            $table->float('fuel_used')->nullable(false);
            $table->float('remaining_virtual_energy')->nullable(false);
            $table->float('virtual_energy_used')->nullable(false);
            $table->float('tire_wear_fl')->nullable(false);
            $table->float('tire_wear_fr')->nullable(false);
            $table->float('tire_wear_rl')->nullable(false);
            $table->float('tire_wear_rr')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lmu_laps');
    }
};
