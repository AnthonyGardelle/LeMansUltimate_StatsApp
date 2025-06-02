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
        Schema::create('lmu_session_participations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('lmu_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->integer('finish_position')->nullable(false);
            $table->integer('class_finish_position')->nullable(false);
            $table->integer('laps_completed')->nullable(false);
            $table->integer('pit_stops_executed')->nullable(false);
            $table->float('best_lap_time')->nullable();
            $table->string('finish_status')->nullable(false);
            $table->string('dnf_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lmu_session_participations');
    }
};
