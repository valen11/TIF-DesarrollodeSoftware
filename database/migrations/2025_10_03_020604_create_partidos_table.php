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
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // 1. Campos principales de identificación
            $table->string('nombre', 255)->unique(); // Nombre completo del partido
            $table->string('siglas', 10)->unique(); // Siglas o acrónimo (ej. 'FpV')
            $table->unsignedSmallInteger('numero_lista')->nullable(); // Número de lista electoral, si aplica
            
            // 2. Información institucional/visual
            $table->string('presidente_nombre', 255)->nullable();
            $table->string('color_hex', 7)->default('#CCCCCC'); // Color para visualizaciones
            
            // 3. Estado
            $table->boolean('activo')->default(true); // Indica si está vigente para elecciones
            $table->date('fundacion_fecha')->nullable();
            
            // Campos automáticos de Laravel
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};
