<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MesasController;
use App\Http\Controllers\CandidatosController;
use App\Http\Controllers\ListasController;
use App\Http\Controllers\ResultadosController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\TelegramaController;
use App\Http\Controllers\ProvinciaController;

Route::get('/test', function() {
    return response()->json(['mensaje' => 'API funcionando correctamente']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('candidatos')->group(function () {
 
    Route::get('/', [CandidatosController::class, 'index']);
    Route::post('/', [CandidatosController::class, 'store']);
    

    Route::get('/cargo/{cargo}', [CandidatosController::class, 'porCargo']);
    Route::get('/lista/{idLista}', [CandidatosController::class, 'porLista']);
    Route::get('/provincia/{idProvincia}', [CandidatosController::class, 'porProvincia']);
    Route::get('/provincia/{idProvincia}/cargo/{cargo}', [CandidatosController::class, 'porProvinciaYCargo']);
    

    Route::get('/{id}', [CandidatosController::class, 'show']);
    Route::get('/{id}/votos', [CandidatosController::class, 'totalVotos']);
    Route::put('/{id}', [CandidatosController::class, 'update']);
    Route::delete('/{id}', [CandidatosController::class, 'destroy']);
});


Route::post('/candidatos/importar', [CandidatosController::class, 'importar']);


Route::get('/mesas', [MesasController::class, 'index']);
Route::post('/mesas', [MesasController::class, 'store']);
Route::get('/mesas/{id}', [MesasController::class, 'show']);
Route::put('/mesas/{id}', [MesasController::class, 'update']);
Route::delete('/mesas/{id}', [MesasController::class, 'destroy']);


Route::get('/mesas/provincia/{idProvincia}', [MesasController::class, 'porProvincia']);
Route::get('/mesas/circuito/{circuito}', [MesasController::class, 'porCircuito']);
Route::get('/mesas/establecimiento/{establecimiento}', [MesasController::class, 'porEstablecimiento']);
Route::post('/mesas/rango', [MesasController::class, 'porRango']);
Route::get('/mesas/{id}/telegramas', [MesasController::class, 'telegramas']);
Route::get('/mesas/{id}/estadisticas', [MesasController::class, 'estadisticas']);


Route::post('/mesas/importar', [MesasController::class, 'importar']);


Route::get('/listas', [ListasController::class, 'index']);
Route::post('/listas', [ListasController::class, 'store']);
Route::get('/listas/{id}', [ListasController::class, 'show']);
Route::put('/listas/{id}', [ListasController::class, 'update']);
Route::delete('/listas/{id}', [ListasController::class, 'destroy']);

Route::get('/listas/provincia/{idProvincia}', [ListasController::class, 'porProvincia']);
Route::get('/listas/cargo/diputados', [ListasController::class, 'conDiputados']);
Route::get('/listas/cargo/senadores', [ListasController::class, 'conSenadores']);
Route::get('/listas/{id}/candidatos', [ListasController::class, 'candidatos']);
Route::get('/listas/{id}/resultados', [ListasController::class, 'resultados']);
Route::post('/listas/importar', [ListasController::class, 'importar']);

Route::get('/resultados', [ResultadosController::class, 'index']);
Route::post('/resultados', [ResultadosController::class, 'store']);
Route::get('/resultados/{id}', [ResultadosController::class, 'show']);
Route::put('/resultados/{id}', [ResultadosController::class, 'update']);
Route::delete('/resultados/{id}', [ResultadosController::class, 'destroy']);

Route::get('/resultados/provincia/{idProvincia}', [ResultadosController::class, 'porProvincia']);
Route::get('/resultados/telegrama/{idTelegrama}', [ResultadosController::class, 'porTelegrama']);
Route::get('/resultados/cargo/{cargo}', [ResultadosController::class, 'porCargo']);
Route::get('/resultados/resumen/nacional', [ResultadosController::class, 'resumenNacional']);
Route::get('/resultados/exportar', [ResultadosController::class, 'exportar']);
Route::post('/resultados/importar', [ResultadosController::class, 'importar']);


Route::get('/personas', [PersonaController::class, 'index']);
Route::post('/personas', [PersonaController::class, 'store']);
Route::get('/personas/{dni}', [PersonaController::class, 'show']);
Route::put('/personas/{dni}', [PersonaController::class, 'update']);
Route::delete('/personas/{dni}', [PersonaController::class, 'destroy']);

Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);


Route::get('/telegramas', [TelegramaController::class, 'index']);
Route::post('/telegramas', [TelegramaController::class, 'store']);
Route::get('/telegramas/{id}', [TelegramaController::class, 'show']);
Route::put('/telegramas/{id}', [TelegramaController::class, 'update']);
Route::delete('/telegramas/{id}', [TelegramaController::class, 'destroy']);

Route::get('/provincias', [ProvinciaController::class, 'index']);
Route::post('/provincias', [ProvinciaController::class, 'store']);
Route::get('/provincias/{id}', [ProvinciaController::class, 'show']);
Route::put('/provincias/{id}', [ProvinciaController::class, 'update']);
Route::delete('/provincias/{id}', [ProvinciaController::class, 'destroy']);

// Listas por provincia
Route::get('/provincias/{id}/listas', [ProvinciaController::class, 'listas']);

// Estadísticas por provincia
Route::get('/provincias/{id}/estadisticas', [ProvinciaController::class, 'estadisticas']);

// Comparar provincias (POST porque recibe array)
Route::post('/provincias/comparar', [ProvinciaController::class, 'comparar']);

// Ranking nacional por votos
Route::get('/provincias-ranking', [ProvinciaController::class, 'ranking']);

// Buscar provincias
Route::get('/provincias-buscar', [ProvinciaController::class, 'buscar']);

// Provincias por región
Route::get('/provincias/region/{region}', [ProvinciaController::class, 'porRegion']);

// Resumen general nacional
Route::get('/provincias-resumen', [ProvinciaController::class, 'resumen']);

// Exportar provincias
Route::get('/provincias-exportar', [ProvinciaController::class, 'exportar']);

// Importar provincias
Route::post('/provincias-importar', [ProvinciaController::class, 'importar']);