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
    Schema::create('candidatos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('lista_id')->constrained()->onDelete('cascade');
        $table->string('nombre_completo');
        $table->integer('orden_en_lista');
        $table->text('observaciones')->nullable();
        $table->timestamps();
        
        $table->unique(['lista_id', 'orden_en_lista']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidatos');
    }
};
