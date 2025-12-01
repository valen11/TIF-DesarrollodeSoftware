<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    // Lista de usuarios
    public function index()
    {
        return Usuario::with('persona')->get();
    }

    // Crear usuario
    public function store(Request $request)
    {
        $request->validate([
            'idUsuario' => 'required|integer|unique:usuarios,idUsuario',
            'nombreDeUsuario' => 'required|string',
            'contrasenia' => 'required|string',
            'rol' => 'required|string',
            'dni' => 'required|string|exists:personas,dni'
        ]);

        $usuario = Usuario::create($request->all());
        return response()->json($usuario, 201);
    }

    // Mostrar usuario
    public function show($idUsuario)
    {
        return Usuario::with('persona')->findOrFail($idUsuario);
    }

    // Actualizar usuario
    public function update(Request $request, $idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);

        $request->validate([
            'nombreDeUsuario' => 'string',
            'contrasenia' => 'string',
            'rol' => 'string',
            'dni' => 'string|exists:personas,dni'
        ]);

        $usuario->update($request->all());
        return response()->json($usuario, 200);
    }

    // Eliminar usuario
    public function destroy($idUsuario)
    {
        Usuario::findOrFail($idUsuario)->delete();
        return response()->json(['mensaje' => 'Usuario eliminado'], 200);
    }
}