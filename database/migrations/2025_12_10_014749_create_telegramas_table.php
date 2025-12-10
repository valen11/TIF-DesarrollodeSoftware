<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
{
    Schema::create('telegramas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mesa_id')->constrained()->onDelete('cascade');
        $table->foreignId('lista_id')->constrained()->onDelete('cascade');
        $table->integer('votos_diputados')->default(0);
        $table->integer('votos_senadores')->default(0);
        $table->integer('votos_blancos')->default(0);
        $table->integer('votos_nulos')->default(0);
        $table->integer('votos_recurridos')->default(0);
        $table->string('usuario_carga')->nullable();
        $table->timestamp('fecha_carga')->useCurrent();
        $table->timestamps();
        
        $table->unique(['mesa_id', 'lista_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegramas');
    }
};
