<?php

namespace App\Http\Controllers;

use App\Models\Telegrama;
use App\Models\Mesa;
use Illuminate\Http\Request;

class TelegramaController extends Controller
{
    // Ver todos los telegramas
    public function index()
    {
        return Telegrama::with(['mesa', 'usuario'])->get();
    }

    // Crear un telegrama
    public function store(Request $request)
    {
        $request->validate([
            'idMesa' => 'required|integer|exists:mesas,idMesa',
            'idUsuario' => 'required|integer|exists:usuarios,idUsuario',
            'votosDiputados' => 'required|integer|min:0',
            'votosSenadores' => 'required|integer|min:0',
            'blancos' => 'required|integer|min:0',
            'nulos' => 'required|integer|min:0',
            'impugnados' => 'required|integer|min:0',
        ]);

        // Verificar electores
        $mesa = Mesa::find($request->idMesa);

        $totalVotos = 
            $request->votosDiputados +
            $request->votosSenadores +
            $request->blancos +
            $request->nulos +
            $request->impugnados;

        if ($totalVotos > $mesa->electores) {
            return response()->json([
                'error' => 'La suma de votos no puede superar la cantidad de electores.'
            ], 400);
        }

        // Crea el telegrama
        $telegrama = Telegrama::create([
            'idTelegrama' => $request->idTelegrama,
            'votosDiputados' => $request->votosDiputados,
            'votosSenadores' => $request->votosSenadores,
            'blancos' => $request->blancos,
            'nulos' => $request->nulos,
            'impugnados' => $request->impugnados,
            'fechaHora' => now(),
            'idMesa' => $request->idMesa,
            'idUsuario' => $request->idUsuario
        ]);

        return response()->json($telegrama, 201);
    }

    // Mostrar 1 telegrama
    public function show($id)
    {
        return Telegrama::with(['mesa', 'usuario'])->findOrFail($id);
    }

    // Actualizar un telegrama
    public function update(Request $request, $id)
    {
        $telegrama = Telegrama::findOrFail($id);

        $request->validate([
            'votosDiputados' => 'integer|min:0',
            'votosSenadores' => 'integer|min:0',
            'blancos' => 'integer|min:0',
            'nulos' => 'integer|min:0',
            'impugnados' => 'integer|min:0'
        ]);

        $data = $request->all();

        // Validar sumatoria de votos
        $mesa = Mesa::find($telegrama->idMesa);

        $totalVotos =
            ($data['votosDiputados'] ?? $telegrama->votosDiputados) +
            ($data['votosSenadores'] ?? $telegrama->votosSenadores) +
            ($data['blancos'] ?? $telegrama->blancos) +
            ($data['nulos'] ?? $telegrama->nulos) +
            ($data['impugnados'] ?? $telegrama->impugnados);

        if ($totalVotos > $mesa->electores) {
            return response()->json([
                'error' => 'La suma de votos no puede superar los electores.'
            ], 400);
        }

        $telegrama->update($data);
        return response()->json($telegrama, 200);
    }

    // Eliminar
    public function destroy($id)
    {
        Telegrama::findOrFail($id)->delete();
        return response()->json(['mensaje' => 'Telegrama eliminado'], 200);
    }
}