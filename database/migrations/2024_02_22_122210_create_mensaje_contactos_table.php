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
        Schema::create('mensaje_contactos', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string("name");
            $table->string("artistic_name")->nullable();
            $table->text('message');
            $table->string('subject');
            $table->boolean('read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensaje_contactos');
    }
};
