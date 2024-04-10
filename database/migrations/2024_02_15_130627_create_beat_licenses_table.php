<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('beat_licenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beat_id');
            $table->unsignedBigInteger('license_id');
            $table->foreign('beat_id')->references('id')->on('beats')->onDelete('cascade');
            $table->foreign('license_id')->references('id')->on('licenses');
            $table->uuid('download_key');;
            $table->float('price');
            $table->string('file_url');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
