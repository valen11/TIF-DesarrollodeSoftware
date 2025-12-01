<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lista;

class ListasController extends Controller
{
    // Listar todas las listas
    public function index()
    {
        $listas = Lista::with('provincia')->get();
        return response()->json($listas);
    }

    // Crear una nueva lista
    public function store(Request $request)
    {
        $request->validate([
            'idLista' => 'required|integer|unique:Lista,idLista',
            'nombre' => 'required|string|max:100',
            'alianza' => 'nullable|string|max:100',
            'cargoDiputado' => 'required|boolean',
            'cargoSenador' => 'required|boolean',
            'idProvincia' => 'required|integer|exists:Provincia,idProvincia',
        ]);

        $lista = Lista::create($request->all());

        return response()->json([
            'mensaje' => 'Lista creada con éxito',
            'lista' => $lista->load('provincia')
        ], 201);
    }

    // Mostrar una lista específica
    public function show($id)
    {
        $lista = Lista::with(['provincia', 'candidatos.persona'])->findOrFail($id);
        return response()->json($lista);
    }

    // Actualizar una lista
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'alianza' => 'nullable|string|max:100',
            'cargoDiputado' => 'sometimes|boolean',
            'cargoSenador' => 'sometimes|boolean',
            'idProvincia' => 'sometimes|integer|exists:Provincia,idProvincia',
        ]);

        $lista = Lista::findOrFail($id);
        $lista->update($request->all());

        return response()->json([
            'mensaje' => 'Lista actualizada con éxito',
            'lista' => $lista->load('provincia')
        ]);
    }

    // Eliminar una lista
    public function destroy($id)
    {
        $lista = Lista::findOrFail($id);
        $lista->delete();

        return response()->json(['mensaje' => 'Lista eliminada correctamente']);
    }

    // Obtener listas por provincia
    public function porProvincia($idProvincia)
    {
        $listas = Lista::where('idProvincia', $idProvincia)
                       ->with(['provincia', 'candidatos'])
                       ->get();
        return response()->json($listas);
    }

    // Obtener listas que participan en Diputados
    public function conDiputados()
    {
        $listas = Lista::where('cargoDiputado', true)
                       ->with('provincia')
                       ->get();
        return response()->json($listas);
    }

    // Obtener listas que participan en Senadores
    public function conSenadores()
    {
        $listas = Lista::where('cargoSenador', true)
                       ->with('provincia')
                       ->get();
        return response()->json($listas);
    }

    // Obtener candidatos de una lista separados por cargo
    public function candidatos($idLista)
    {
        $lista = Lista::with(['candidatos.persona', 'provincia'])->findOrFail($idLista);
        
        return response()->json([
            'lista' => $lista->nombre,
            'alianza' => $lista->alianza,
            'provincia' => $lista->provincia->nombre ?? 'Sin provincia',
            'candidatos_diputados' => $lista->candidatos()
                                            ->where('cargo', 'DIPUTADOS')
                                            ->orderBy('orden_en_lista')
                                            ->with('persona')
                                            ->get(),
            'candidatos_senadores' => $lista->candidatos()
                                            ->where('cargo', 'SENADORES')
                                            ->orderBy('orden_en_lista')
                                            ->with('persona')
                                            ->get(),
        ]);
    }

    // Obtener resultados (votos) de una lista
    public function resultados($idLista)
    {
        $lista = Lista::with(['resultados.telegrama', 'provincia'])->findOrFail($idLista);
        
        $totalVotos = $lista->resultados->sum('votos');
        
        return response()->json([
            'lista' => $lista->nombre,
            'alianza' => $lista->alianza,
            'provincia' => $lista->provincia->nombre ?? 'Sin provincia',
            'total_votos' => $totalVotos,
            'resultados_por_mesa' => $lista->resultados
        ]);
    }

    // Importar listas desde CSV/JSON
    public function importar(Request $request)
    {
        $request->validate([
            'listas' => 'required|array',
            'listas.*.idLista' => 'required|integer|unique:Lista,idLista',
            'listas.*.nombre' => 'required|string|max:100',
            'listas.*.alianza' => 'nullable|string|max:100',
            'listas.*.cargoDiputado' => 'required|boolean',
            'listas.*.cargoSenador' => 'required|boolean',
            'listas.*.idProvincia' => 'required|integer|exists:Provincia,idProvincia',
        ]);

        $listasCreadas = [];
        foreach ($request->listas as $listaData) {
            $listasCreadas[] = Lista::create($listaData);
        }

        return response()->json([
            'mensaje' => 'Listas importadas con éxito',
            'total' => count($listasCreadas),
            'listas' => $listasCreadas
        ], 201);
    }
}