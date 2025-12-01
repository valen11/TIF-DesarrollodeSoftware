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
        Schema::create('listas', function (Blueprint $table) {
            $table->id('idLista');
            $table->string('nombre');
            $table->string('alianza')->nullable();
            $table->string('cargoDiputado')->nullable();
            $table->string('cargoSenador')->nullable();

            $table->unsignedBigInteger('idProvincia');

            $table->timestamps();

            $table->foreign('idProvincia')
                ->references('idProvincia')
                ->on('provincias')
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
        Schema::dropIfExists('listas');
    }
};
