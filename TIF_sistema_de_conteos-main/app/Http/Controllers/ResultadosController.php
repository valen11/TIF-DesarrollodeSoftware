<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resultado;
use App\Models\Lista;
use App\Models\Telegrama;
use Illuminate\Support\Facades\DB;

class ResultadosController extends Controller
{
    // Listar todos los resultados
    public function index()
    {
        $resultados = Resultado::with(['lista', 'telegrama.mesa'])->get();
        return response()->json($resultados);
    }

    // Crear un nuevo resultado
    public function store(Request $request)
    {
        $request->validate([
            'idResultado' => 'required|integer|unique:Resultado,idResultado',
            'votos' => 'required|integer|min:0',
            'porcentaje' => 'nullable|numeric|min:0|max:100',
            'idLista' => 'required|integer|exists:Lista,idLista',
            'idTelegrama' => 'required|integer|exists:Telegrama,idTelegrama',
        ]);

        $resultado = Resultado::create($request->all());

        return response()->json([
            'mensaje' => 'Resultado creado con éxito',
            'resultado' => $resultado->load(['lista', 'telegrama'])
        ], 201);
    }

    // Mostrar un resultado específico
    public function show($id)
    {
        $resultado = Resultado::with(['lista.provincia', 'telegrama.mesa'])->findOrFail($id);
        return response()->json($resultado);
    }

    // Actualizar un resultado
    public function update(Request $request, $id)
    {
        $request->validate([
            'votos' => 'sometimes|integer|min:0',
            'porcentaje' => 'nullable|numeric|min:0|max:100',
            'idLista' => 'sometimes|integer|exists:Lista,idLista',
            'idTelegrama' => 'sometimes|integer|exists:Telegrama,idTelegrama',
        ]);

        $resultado = Resultado::findOrFail($id);
        $resultado->update($request->all());

        return response()->json([
            'mensaje' => 'Resultado actualizado con éxito',
            'resultado' => $resultado->load(['lista', 'telegrama'])
        ]);
    }

    // Eliminar un resultado
    public function destroy($id)
    {
        $resultado = Resultado::findOrFail($id);
        $resultado->delete();

        return response()->json(['mensaje' => 'Resultado eliminado correctamente']);
    }

    // Resultados por provincia (REQUERIMIENTO DEL TP)
    public function porProvincia($idProvincia)
    {
        // Total de votos por lista en la provincia
        $resultados = Resultado::select('idLista', DB::raw('SUM(votos) as total_votos'))
                               ->whereHas('lista', function($query) use ($idProvincia) {
                                   $query->where('idProvincia', $idProvincia);
                               })
                               ->groupBy('idLista')
                               ->with('lista.provincia')
                               ->get();

        $totalVotos = $resultados->sum('total_votos');

        // Calcular porcentajes y determinar más votado
        $resultadosConPorcentaje = $resultados->map(function($resultado) use ($totalVotos) {
            $porcentaje = $totalVotos > 0 ? round(($resultado->total_votos / $totalVotos) * 100, 2) : 0;
            return [
                'lista' => $resultado->lista->nombre,
                'alianza' => $resultado->lista->alianza,
                'votos' => $resultado->total_votos,
                'porcentaje' => $porcentaje,
            ];
        })->sortByDesc('votos')->values();

        // Marcar el más votado
        if ($resultadosConPorcentaje->isNotEmpty()) {
            $resultadosConPorcentaje[0]['estado'] = 'más votado';
            $resultadosConPorcentaje = $resultadosConPorcentaje->map(function($item, $index) {
                if ($index > 0) {
                    $item['estado'] = 'otros';
                }
                return $item;
            });
        }

        return response()->json([
            'provincia' => $resultados->first()->lista->provincia->nombre ?? 'Desconocida',
            'total_votos' => $totalVotos,
            'resultados' => $resultadosConPorcentaje
        ]);
    }

    // Resultados por telegrama
    public function porTelegrama($idTelegrama)
    {
        $resultados = Resultado::where('idTelegrama', $idTelegrama)
                               ->with(['lista', 'telegrama.mesa'])
                               ->get();

        $totalVotos = $resultados->sum('votos');

        return response()->json([
            'telegrama' => $idTelegrama,
            'mesa' => $resultados->first()->telegrama->mesa ?? null,
            'total_votos' => $totalVotos,
            'resultados' => $resultados
        ]);
    }

    // Resumen nacional (agregado de todas las provincias) - REQUERIMIENTO DEL TP
    public function resumenNacional()
    {
        // Totales por lista a nivel nacional
        $resultados = Resultado::select('idLista', DB::raw('SUM(votos) as total_votos'))
                               ->groupBy('idLista')
                               ->with('lista.provincia')
                               ->get();

        $totalVotosNacional = $resultados->sum('total_votos');

        // Ranking de listas
        $ranking = $resultados->map(function($resultado) use ($totalVotosNacional) {
            return [
                'lista' => $resultado->lista->nombre,
                'alianza' => $resultado->lista->alianza,
                'votos' => $resultado->total_votos,
                'porcentaje' => $totalVotosNacional > 0 ? round(($resultado->total_votos / $totalVotosNacional) * 100, 2) : 0,
            ];
        })->sortByDesc('votos')->values();

        return response()->json([
            'total_votos_nacional' => $totalVotosNacional,
            'ranking_listas' => $ranking
        ]);
    }

    // Resultados por cargo (Diputados o Senadores)
    public function porCargo($cargo)
    {
        // Validar que el cargo sea válido
        if (!in_array(strtoupper($cargo), ['DIPUTADOS', 'SENADORES'])) {
            return response()->json(['error' => 'Cargo inválido. Use DIPUTADOS o SENADORES'], 400);
        }

        $campo = strtoupper($cargo) === 'DIPUTADOS' ? 'cargoDiputado' : 'cargoSenador';

        $resultados = Resultado::select('idLista', DB::raw('SUM(votos) as total_votos'))
                               ->whereHas('lista', function($query) use ($campo) {
                                   $query->where($campo, true);
                               })
                               ->groupBy('idLista')
                               ->with('lista')
                               ->get();

        $totalVotos = $resultados->sum('total_votos');

        $resultadosConPorcentaje = $resultados->map(function($resultado) use ($totalVotos) {
            return [
                'lista' => $resultado->lista->nombre,
                'alianza' => $resultado->lista->alianza,
                'votos' => $resultado->total_votos,
                'porcentaje' => $totalVotos > 0 ? round(($resultado->total_votos / $totalVotos) * 100, 2) : 0,
            ];
        })->sortByDesc('votos')->values();

        return response()->json([
            'cargo' => strtoupper($cargo),
            'total_votos' => $totalVotos,
            'resultados' => $resultadosConPorcentaje
        ]);
    }

    // Exportar resultados en formato JSON (para CSV)
    public function exportar(Request $request)
    {
        $idProvincia = $request->input('idProvincia');
        $cargo = $request->input('cargo');

        $query = Resultado::with(['lista.provincia', 'telegrama.mesa']);

        if ($idProvincia) {
            $query->whereHas('lista', function($q) use ($idProvincia) {
                $q->where('idProvincia', $idProvincia);
            });
        }

        if ($cargo) {
            $campo = strtoupper($cargo) === 'DIPUTADOS' ? 'cargoDiputado' : 'cargoSenador';
            $query->whereHas('lista', function($q) use ($campo) {
                $q->where($campo, true);
            });
        }

        $resultados = $query->get();

        // Formato simplificado para exportar
        $datosExportacion = $resultados->map(function($resultado) {
            return [
                'id_resultado' => $resultado->idResultado,
                'lista' => $resultado->lista->nombre,
                'alianza' => $resultado->lista->alianza,
                'provincia' => $resultado->lista->provincia->nombre ?? 'N/A',
                'votos' => $resultado->votos,
                'porcentaje' => $resultado->porcentaje,
                'id_telegrama' => $resultado->idTelegrama,
                'id_mesa' => $resultado->telegrama->idMesa ?? 'N/A',
            ];
        });

        return response()->json($datosExportacion);
    }

    // Importar resultados desde CSV/JSON
    public function importar(Request $request)
    {
        $request->validate([
            'resultados' => 'required|array',
            'resultados.*.idResultado' => 'required|integer|unique:Resultado,idResultado',
            'resultados.*.votos' => 'required|integer|min:0',
            'resultados.*.porcentaje' => 'nullable|numeric|min:0|max:100',
            'resultados.*.idLista' => 'required|integer|exists:Lista,idLista',
            'resultados.*.idTelegrama' => 'required|integer|exists:Telegrama,idTelegrama',
        ]);

        $resultadosCreados = [];
        foreach ($request->resultados as $resultadoData) {
            $resultadosCreados[] = Resultado::create($resultadoData);
        }

        return response()->json([
            'mensaje' => 'Resultados importados con éxito',
            'total' => count($resultadosCreados),
            'resultados' => $resultadosCreados
        ], 201);
    }
}