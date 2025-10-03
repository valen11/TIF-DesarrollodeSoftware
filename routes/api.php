<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\partidocontroller;


Route::get('/partidospoliticos', [partidocontroller::class, 'index']);


Route::get('/partidospoliticos/{id}', function (){
    return 'Obteniendo un partido politico';
});

Route::post('/partidospoliticos', function (){
    return 'Creando partido politico';
});

Route::put('/partidospoliticos/{id}', function (){
    return 'Actualizando lista de partido politico';
});

Route::delete('/partidospoliticos', function (){
    return 'Eliminando partido politico';
});


