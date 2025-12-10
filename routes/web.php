<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ResultadoController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\ListaController;
use App\Http\Controllers\CandidatoController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\TelegramaController;

Route::get('/', function () {
    return view('welcome');  // ← Laravel incluye esta vista
})->name('home');

// CRUD Básicos
Route::resource('provincias', ProvinciaController::class);
Route::resource('listas', ListaController::class);
Route::resource('candidatos', CandidatoController::class);
Route::resource('mesas', MesaController::class);
Route::resource('telegramas', TelegramaController::class);

// Importación
Route::post('/importar/listas', [ImportController::class, 'importarListas'])->name('importar.listas');
Route::post('/importar/candidatos', [ImportController::class, 'importarCandidatos'])->name('importar.candidatos');
Route::post('/importar/mesas', [ImportController::class, 'importarMesas'])->name('importar.mesas');
Route::post('/importar/telegramas', [ImportController::class, 'importarTelegramas'])->name('importar.telegramas');

// Resultados
Route::get('/resultados/provincia/{provincia}', [ResultadoController::class, 'porProvincia'])->name('resultados.provincia');
Route::get('/resultados/nacional', [ResultadoController::class, 'nacional'])->name('resultados.nacional');
Route::get('/exportar/provincia/{provincia}', [ResultadoController::class, 'exportarProvincia'])->name('exportar.provincia');
