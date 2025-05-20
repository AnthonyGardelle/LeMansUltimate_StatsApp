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
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('track_venue')->nullable(false);
            $table->string('track_course')->nullable(false);
            $table->string('track_event')->nullable(false);
            $table->float('track_length')->nullable(false);
            $table->unique(['track_venue', 'track_course', 'track_event', 'track_length']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
