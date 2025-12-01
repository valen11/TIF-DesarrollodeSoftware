<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    // Mostrar todas las personas
    public function index()
    {
        return Persona::all();
    }

    // Crear una persona nueva
    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|unique:personas,dni',
            'nombre' => 'required|string',
            'apellido' => 'required|string',
        ]);

        $persona = Persona::create($request->all());
        return response()->json($persona, 201);
    }

    // Mostrar una persona por DNI
    public function show($dni)
    {
        $persona = Persona::findOrFail($dni);
        return $persona;
    }

    // Actualizar una persona
    public function update(Request $request, $dni)
    {
        $persona = Persona::findOrFail($dni);

        $request->validate([
            'nombre' => 'string',
            'apellido' => 'string'
        ]);

        $persona->update($request->all());
        return response()->json($persona, 200);
    }

    // Eliminar una persona
    public function destroy($dni)
    {
        $persona = Persona::findOrFail($dni);
        $persona->delete();

        return response()->json(['mensaje' => 'Persona eliminada'], 200);
    }
}