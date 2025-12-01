<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Models\Lista;
use App\Models\Resultado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProvinciaController extends Controller
{
    /**
     * Listar todas las provincias
     */
    public function index()
    {
        $provincias = Provincia::all();

        return response()->json($provincias);
    }

    /**
     * Crear una nueva provincia
     */
    public function store(Request $request)
    {
        $request->validate([
            'idProvincia' => 'required|integer|unique:Provincia,idProvincia',
            'nombre' => 'required|string|max:255|unique:Provincia,nombre',
            'codigo' => 'nullable|string|max:10',
            'region' => 'nullable|string|max:100',
        ]);

        $provincia = Provincia::create($request->all());

        return response()->json([
            'mensaje' => 'Provincia creada con éxito',
            'provincia' => $provincia,
        ], 201);
    }

    /**
     * Mostrar una provincia específica
     */
    public function show($id)
    {
        $provincia = Provincia::findOrFail($id);

        return response()->json($provincia);
    }

    /**
     * Actualizar una provincia
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'sometimes|string|max:255|unique:Provincia,nombre,' . $id . ',idProvincia',
            'codigo' => 'nullable|string|max:10',
            'region' => 'nullable|string|max:100',
        ]);

        $provincia = Provincia::findOrFail($id);
        $provincia->update($request->all());

        return response()->json([
            'mensaje' => 'Provincia actualizada con éxito',
            'provincia' => $provincia,
        ]);
    }

    /**
     * Eliminar una provincia
     */
    public function destroy($id)
    {
        $provincia = Provincia::findOrFail($id);

        // Verificar si tiene listas asociadas
        $tieneListas = Lista::where('idProvincia', $id)->exists();

        if ($tieneListas) {
            return response()->json([
                'error' => 'No se puede eliminar la provincia porque tiene listas asociadas',
            ], 400);
        }

        $provincia->delete();

        return response()->json([
            'mensaje' => 'Provincia eliminada correctamente',
        ]);
    }

    /**
     * Obtener listas por provincia
     */
    public function listas($id)
    {
        $provincia = Provincia::findOrFail($id);

        $listas = Lista::where('idProvincia', $id)
                      ->get();

        return response()->json([
            'provincia' => $provincia->nombre,
            'total_listas' => $listas->count(),
            'listas' => $listas,
        ]);
    }

    /**
     * Estadísticas de una provincia
     */
    public function estadisticas($id)
    {
        $provincia = Provincia::findOrFail($id);

        // Total de listas
        $totalListas = Lista::where('idProvincia', $id)->count();

        // Total de votos en la provincia
        $totalVotos = Resultado::whereHas('lista', function ($query) use ($id) {
            $query->where('idProvincia', $id);
        })->sum('votos');

        // Lista más votada
        $listaMasVotada = Resultado::select('idLista', DB::raw('SUM(votos) as total_votos'))
                                   ->whereHas('lista', function ($query) use ($id) {
                                       $query->where('idProvincia', $id);
                                   })
                                   ->groupBy('idLista')
                                   ->orderByDesc('total_votos')
                                   ->with('lista')
                                   ->first();

        // Participación por cargo
        $listasDiputados = Lista::where('idProvincia', $id)
                                ->where('cargoDiputado', true)
                                ->count();

        $listasSenadores = Lista::where('idProvincia', $id)
                                ->where('cargoSenador', true)
                                ->count();

        return response()->json([
            'provincia' => $provincia->nombre,
            'estadisticas' => [
                'total_listas' => $totalListas,
                'total_votos' => $totalVotos,
                'listas_diputados' => $listasDiputados,
                'listas_senadores' => $listasSenadores,
                'lista_mas_votada' => $listaMasVotada ? [
                    'nombre' => $listaMasVotada->lista->nombre,
                    'alianza' => $listaMasVotada->lista->alianza,
                    'votos' => $listaMasVotada->total_votos,
                ] : null,
            ],
        ]);
    }

    /**
     * Comparar provincias
     */
    public function comparar(Request $request)
    {
        $request->validate([
            'provincias' => 'required|array|min:2',
            'provincias.*' => 'exists:Provincia,idProvincia',
        ]);

        $provinciasIds = $request->provincias;

        $comparacion = Provincia::whereIn('idProvincia', $provinciasIds)
                                ->get()
                                ->map(function ($provincia) {
                                    $totalVotos = Resultado::whereHas('lista', function ($query) use ($provincia) {
                                        $query->where('idProvincia', $provincia->idProvincia);
                                    })->sum('votos');

                                    $totalListas = Lista::where('idProvincia', $provincia->idProvincia)->count();

                                    return [
                                        'id' => $provincia->idProvincia,
                                        'nombre' => $provincia->nombre,
                                        'total_votos' => $totalVotos,
                                        'total_listas' => $totalListas,
                                        'promedio_votos_por_lista' => $totalListas > 0 
                                            ? round($totalVotos / $totalListas, 2) 
                                            : 0,
                                    ];
                                });

        return response()->json([
            'comparacion' => $comparacion,
        ]);
    }

    /**
     * Ranking de provincias por votos totales
     */
    public function ranking()
    {
        $provincias = Provincia::all();

        $ranking = $provincias->map(function ($provincia) {
            $totalVotos = Resultado::whereHas('lista', function ($query) use ($provincia) {
                $query->where('idProvincia', $provincia->idProvincia);
            })->sum('votos');

            return [
                'id' => $provincia->idProvincia,
                'nombre' => $provincia->nombre,
                'total_votos' => $totalVotos,
            ];
        })->sortByDesc('total_votos')->values();

        return response()->json([
            'ranking' => $ranking,
        ]);
    }

    /**
     * Buscar provincias
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $query = $request->q;

        $provincias = Provincia::where('nombre', 'LIKE', "%{$query}%")
                               ->orWhere('codigo', 'LIKE', "%{$query}%")
                               ->orWhere('region', 'LIKE', "%{$query}%")
                               ->get();

        return response()->json([
            'total' => $provincias->count(),
            'provincias' => $provincias,
        ]);
    }

    /**
     * Obtener provincias por región
     */
    public function porRegion($region)
    {
        $provincias = Provincia::where('region', $region)->get();

        if ($provincias->isEmpty()) {
            return response()->json([
                'mensaje' => 'No se encontraron provincias en esta región',
            ], 404);
        }

        return response()->json([
            'region' => $region,
            'total' => $provincias->count(),
            'provincias' => $provincias,
        ]);
    }

    /**
     * Resumen general de todas las provincias
     */
    public function resumen()
    {
        $totalProvincias = Provincia::count();

        $provinciaConMasVotos = Provincia::select('Provincia.*')
            ->leftJoin('Lista', 'Provincia.idProvincia', '=', 'Lista.idProvincia')
            ->leftJoin('Resultado', 'Lista.idLista', '=', 'Resultado.idLista')
            ->groupBy('Provincia.idProvincia', 'Provincia.nombre')
            ->orderByRaw('SUM(Resultado.votos) DESC')
            ->first();

        $provinciaConMasListas = Provincia::select('Provincia.*')
            ->leftJoin('Lista', 'Provincia.idProvincia', '=', 'Lista.idProvincia')
            ->groupBy('Provincia.idProvincia', 'Provincia.nombre')
            ->orderByRaw('COUNT(Lista.idLista) DESC')
            ->first();

        $totalVotosNacional = Resultado::sum('votos');

        return response()->json([
            'total_provincias' => $totalProvincias,
            'total_votos_nacional' => $totalVotosNacional,
            'provincia_con_mas_votos' => $provinciaConMasVotos ? [
                'id' => $provinciaConMasVotos->idProvincia,
                'nombre' => $provinciaConMasVotos->nombre,
            ] : null,
            'provincia_con_mas_listas' => $provinciaConMasListas ? [
                'id' => $provinciaConMasListas->idProvincia,
                'nombre' => $provinciaConMasListas->nombre,
            ] : null,
        ]);
    }

    /**
     * Exportar provincias
     */
    public function exportar()
    {
        $provincias = Provincia::all()->map(function ($provincia) {
            $totalVotos = Resultado::whereHas('lista', function ($query) use ($provincia) {
                $query->where('idProvincia', $provincia->idProvincia);
            })->sum('votos');

            $totalListas = Lista::where('idProvincia', $provincia->idProvincia)->count();

            return [
                'id' => $provincia->idProvincia,
                'nombre' => $provincia->nombre,
                'codigo' => $provincia->codigo,
                'region' => $provincia->region,
                'total_listas' => $totalListas,
                'total_votos' => $totalVotos,
            ];
        });

        return response()->json($provincias);
    }

    /**
     * Importar provincias
     */
    public function importar(Request $request)
    {
        $request->validate([
            'provincias' => 'required|array',
            'provincias.*.idProvincia' => 'required|integer|unique:Provincia,idProvincia',
            'provincias.*.nombre' => 'required|string|max:255|unique:Provincia,nombre',
            'provincias.*.codigo' => 'nullable|string|max:10',
            'provincias.*.region' => 'nullable|string|max:100',
        ]);

        $provinciasCreadas = [];

        foreach ($request->provincias as $provinciaData) {
            $provinciasCreadas[] = Provincia::create($provinciaData);
        }

        return response()->json([
            'mensaje' => 'Provincias importadas con éxito',
            'total' => count($provinciasCreadas),
            'provincias' => $provinciasCreadas,
        ], 201);
    }
}
