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
        Schema::create('beats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('scale')->nullable();
            $table->string("tagged_path");
            $table->boolean('active')->default(true);
            $table->integer('bpm');
            $table->string('cover_path');
            $table->tinyInteger('stock');
            $table->boolean('still_exclusive')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beats');
    }
};
