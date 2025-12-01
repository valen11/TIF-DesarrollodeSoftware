<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesa;

class MesasController extends Controller
{
    // Listar todas las mesas
    public function index()
    {
        $mesas = Mesa::with('provincia')->get();
        return response()->json($mesas);
    }

    // Crear una nueva mesa
    public function store(Request $request)
    {
        $request->validate([
            'idMesa' => 'required|integer|unique:Mesa,idMesa',
            'electores' => 'required|integer|min:0',
            'establecimiento' => 'required|string|max:255',
            'circuito' => 'required|string|max:100',
            'idProvincia' => 'required|integer|exists:Provincia,idProvincia',
        ]);

        $mesa = Mesa::create($request->all());

        return response()->json([
            'mensaje' => 'Mesa creada con éxito',
            'mesa' => $mesa->load('provincia')
        ], 201);
    }

    // Mostrar una mesa específica
    public function show($id)
    {
        $mesa = Mesa::with(['provincia', 'telegramas'])->findOrFail($id);
        return response()->json($mesa);
    }

    // Actualizar una mesa
    public function update(Request $request, $id)
    {
        $request->validate([
            'electores' => 'sometimes|integer|min:0',
            'establecimiento' => 'sometimes|string|max:255',
            'circuito' => 'sometimes|string|max:100',
            'idProvincia' => 'sometimes|integer|exists:Provincia,idProvincia',
        ]);

        $mesa = Mesa::findOrFail($id);
        $mesa->update($request->all());

        return response()->json([
            'mensaje' => 'Mesa actualizada con éxito',
            'mesa' => $mesa->load('provincia')
        ]);
    }

    // Eliminar una mesa
    public function destroy($id)
    {
        $mesa = Mesa::findOrFail($id);
        $mesa->delete();

        return response()->json(['mensaje' => 'Mesa eliminada correctamente']);
    }

    // Obtener mesas por provincia
    public function porProvincia($idProvincia)
    {
        $mesas = Mesa::where('idProvincia', $idProvincia)
                     ->with('provincia')
                     ->get();
        return response()->json($mesas);
    }

    // Obtener mesas por circuito
    public function porCircuito($circuito)
    {
        $mesas = Mesa::where('circuito', $circuito)
                     ->with('provincia')
                     ->get();
        return response()->json($mesas);
    }

    // Obtener mesas por establecimiento
    public function porEstablecimiento($establecimiento)
    {
        $mesas = Mesa::where('establecimiento', 'LIKE', "%{$establecimiento}%")
                     ->with('provincia')
                     ->get();
        return response()->json($mesas);
    }

    // Obtener mesas por rango de IDs (requerimiento del TP)
    public function porRango(Request $request)
    {
        $request->validate([
            'desde' => 'required|integer',
            'hasta' => 'required|integer|gte:desde',
        ]);

        $mesas = Mesa::whereBetween('idMesa', [$request->desde, $request->hasta])
                     ->with('provincia')
                     ->get();
        
        return response()->json($mesas);
    }

    // Obtener telegramas de una mesa
    public function telegramas($idMesa)
    {
        $mesa = Mesa::with(['telegramas.resultados.lista', 'provincia'])->findOrFail($idMesa);
        
        return response()->json([
            'mesa' => [
                'id' => $mesa->idMesa,
                'establecimiento' => $mesa->establecimiento,
                'circuito' => $mesa->circuito,
                'electores' => $mesa->electores,
                'provincia' => $mesa->provincia->nombre ?? 'Sin provincia'
            ],
            'telegramas' => $mesa->telegramas
        ]);
    }

    // Estadísticas de una mesa
    public function estadisticas($idMesa)
    {
        $mesa = Mesa::with(['telegramas.resultados', 'provincia'])->findOrFail($idMesa);
        
        $totalTelegramas = $mesa->telegramas->count();
        $votosEmitidos = $mesa->telegramas->sum(function($telegrama) {
            return $telegrama->resultados->sum('votos');
        });
        
        $participacion = $mesa->electores > 0 
            ? round(($votosEmitidos / $mesa->electores) * 100, 2) 
            : 0;

        return response()->json([
            'mesa' => [
                'id' => $mesa->idMesa,
                'establecimiento' => $mesa->establecimiento,
                'provincia' => $mesa->provincia->nombre ?? 'Sin provincia'
            ],
            'electores' => $mesa->electores,
            'votos_emitidos' => $votosEmitidos,
            'participacion' => $participacion . '%',
            'total_telegramas' => $totalTelegramas
        ]);
    }

    // Importar mesas desde CSV/JSON
    public function importar(Request $request)
    {
        $request->validate([
            'mesas' => 'required|array',
            'mesas.*.idMesa' => 'required|integer|unique:Mesa,idMesa',
            'mesas.*.electores' => 'required|integer|min:0',
            'mesas.*.establecimiento' => 'required|string|max:255',
            'mesas.*.circuito' => 'required|string|max:100',
            'mesas.*.idProvincia' => 'required|integer|exists:Provincia,idProvincia',
        ]);

        $mesasCreadas = [];
        foreach ($request->mesas as $mesaData) {
            $mesasCreadas[] = Mesa::create($mesaData);
        }

        return response()->json([
            'mensaje' => 'Mesas importadas con éxito',
            'total' => count($mesasCreadas),
            'mesas' => $mesasCreadas
        ], 201);
    }
}