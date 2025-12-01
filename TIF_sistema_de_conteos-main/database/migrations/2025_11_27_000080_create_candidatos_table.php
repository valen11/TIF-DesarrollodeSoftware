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
        Schema::create('candidatos', function (Blueprint $table) {
            $table->id('idCandidato');
            $table->string('dni');
            $table->string('cargo');
            $table->integer('ordenEnLista');
            $table->string('nombre');
            $table->string('apellido');


            $table->unsignedBigInteger('idLista');


            $table->timestamps();


            $table->foreign('dni')
            ->references('dni')
            ->on('personas')
            ->onDelete('cascade');


            $table->foreign('idLista')
            ->references('idLista')
            ->on('listas')
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
        Schema::dropIfExists('candidatos');
    }
};
