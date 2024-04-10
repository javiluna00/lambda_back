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
        Schema::create('beat_mood', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beat_id');
            $table->unsignedBigInteger('mood_id');
            $table->foreign('beat_id')->references('id')->on('beats')->onDelete('cascade');
            $table->foreign('mood_id')->references('id')->on('moods')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beat_mood');
    }
};
