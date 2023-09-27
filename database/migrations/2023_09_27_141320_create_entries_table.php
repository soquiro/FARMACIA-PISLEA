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
        Schema::create('entries', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedTinyInteger('entidad_id');
            $table->unsignedInteger('tipo_documento_id');
            $table->integer('numero');
            $table->datetime('fecha_ingreso');
            $table->unsignedInteger('proveedor_id');
            $table->integer('num_factura');
            $table->string('observaciones');
            $table->integer('usr');
            $table->integer('estado_id');
            $table->integer('usr_mod');
            $table->datetime('fhr_mod');

            $table->timestamps();

            $table->foreign ('tipo_documento_id')->references('id')->on('document_types');
            $table->foreign('entidad_id')->references('id')->on('entities');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
