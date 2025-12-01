<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resultados', function (Blueprint $table) {
            $table->id('idResultado');
            $table->integer('votos');
            $table->decimal('porcentaje', 5, 2);


            $table->unsignedBigInteger('idLista');
            $table->unsignedBigInteger('idTelegrama');


            $table->timestamps();


            $table->foreign('idLista')
            ->references('idLista')
            ->on('listas')
            ->onDelete('cascade');


            $table->foreign('idTelegrama')
            ->references('idTelegrama')
            ->on('telegramas')
            ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resultados');
    }
};
