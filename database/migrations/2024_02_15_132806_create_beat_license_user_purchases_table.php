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
        Schema::create('beat_license_user_purchases', function (Blueprint $table) {
            $table->id();


            $table->unsignedBigInteger('beat_license_id');
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('email');

            $table->integer('price');


            $table->foreign('beat_license_id')->references('id')->on('beat_licenses');
            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beat_licencia_user_compras');
    }
};
