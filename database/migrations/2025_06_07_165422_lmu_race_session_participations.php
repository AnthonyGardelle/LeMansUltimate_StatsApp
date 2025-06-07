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
        Schema::create('lmu_race_session_participations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('lmu_session_participation_id')->onDelete('cascade');
            $table->integer('grid_position')->nullable(false);
            $table->integer('class_grid_position')->nullable(false);
            $table->float('finish_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lmu_race_session_participations');
    }
};
