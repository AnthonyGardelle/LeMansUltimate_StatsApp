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
        Schema::create('lmu_compounds', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('front_compound')->nullable(false);
            $table->string('rear_compound')->nullable(false);
            $table->unique(['front_compound', 'rear_compound']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lmu_compounds');
    }
};
