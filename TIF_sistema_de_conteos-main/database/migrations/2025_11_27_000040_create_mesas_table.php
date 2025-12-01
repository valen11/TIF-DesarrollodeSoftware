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
        Schema::create('mesas', function (Blueprint $table) {
            $table->id('idMesa');
            $table->integer('electores');
            $table->string('establecimiento');
            $table->string('circuito');

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
        Schema::dropIfExists('mesas');
    }
};
