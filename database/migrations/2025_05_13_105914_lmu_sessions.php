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
        Schema::create('lmu_sessions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('lmu_session_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('track_id')->constrained()->onDelete('cascade');
            $table->foreignId('lmu_session_group_id')->constrained()->onDelete('cascade');
            $table->dateTime('starting_at')->nullable(false);
            $table->integer('duration')->nullable(false);
            $table->integer('mech_fail_rate')->nullable(false);
            $table->integer('damage_multiplier')->nullable(false);
            $table->integer('fuel_multiplier')->nullable(false);
            $table->integer('tire_multiplier')->nullable(false);
            $table->integer('parc_ferme')->nullable(false);
            $table->integer('fixed_setups')->nullable(false);
            $table->integer('free_settings')->nullable(false);
            $table->integer('fixed_upgrades')->nullable(false);
            $table->boolean('limited_tires')->nullable(false);
            $table->boolean('tire_warmers')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lmu_sessions');
    }
};
