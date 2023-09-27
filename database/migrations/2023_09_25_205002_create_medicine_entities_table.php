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
        Schema::create('medicine_entities', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedTinyInteger('entidad_id');
            $table->unsignedInteger('medicamento_id');
            $table->integer('stockmax');
            $table->integer('stockmin');
            $table->integer('darmax');
            $table->integer('darmin');
            $table->integer('usr');
            $table->integer('estado_id');
            $table->timestamps();


            $table->foreign ('entidad_id')->references('id')->on('entities');
            $table->foreign ('medicamento_id')->references('id')->on('medicines');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_entities');
    }
};
