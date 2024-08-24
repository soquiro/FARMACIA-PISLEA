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
        Schema::create('entry_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ingreso_id')->indice();
            $table->unsignedInteger('med_entidad_id')->indice();
            $table->string('lote')->indice();
            $table->date('fecha_vencimiento')->indice();
            $table->integer('cantidad');
            $table->decimal('costo_unitario',15,4);
            $table->decimal('costo_total',15,4);
            $table->string('observaciones');
            $table->unsignedInteger('estado_id');
            $table->integer('usuario');
            $table->integer('item_id')->nullable();
            $table->integer('egresodetalle_id')->nullable(); /*codigo del egreso reingresado*/


            $table->timestamps();

            $table->foreign ('ingreso_id')->references('id')->on('entries');
            $table->foreign ('med_entidad_id')->references('id')->on('medicine_entities');
            $table->foreign ('egresodetalle_id')->references('id')->on('discharge_details');



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_details');
    }
};
