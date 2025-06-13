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
        Schema::create('lmu_lap_sectors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('lmu_lap_id')->constrained()->onDelete('cascade');
            $table->integer('sector_number')->nullable(false);
            $table->float('sector_time')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lmu_lap_sectors');
    }
};
