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
        Schema::create('lmu_session_groups', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->dateTime('starting_at')->nullable(false);
            $table->string('hashcode')->unique(); // <-- Add this line
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lmu_session_groups');
    }
};
