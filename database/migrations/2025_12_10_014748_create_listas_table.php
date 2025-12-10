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
    Schema::create('listas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('provincia_id')->constrained()->onDelete('cascade');
        $table->enum('cargo', ['DIPUTADOS', 'SENADORES']);
        $table->string('nombre');
        $table->string('alianza')->nullable();
        $table->timestamps();
        
        $table->unique(['provincia_id', 'cargo', 'nombre']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listas');
    }
};
