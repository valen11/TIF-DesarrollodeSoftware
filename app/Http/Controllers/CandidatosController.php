<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidato;

class CandidatosController extends Controller
{
    // Listar todos los candidatos
    public function index()
    {  
        $candidatos = Candidato::with(['persona', 'lista.provincia'])->get(); 
        return response()->json($candidatos);
    }

    // Crear un nuevo candidato
    public function store(Request $request)
    {
        $request->validate([
            'idCandidato' => 'required|integer|unique:Candidato,idCandidato',
            'cargo' => 'required|in:DIPUTADOS,SENADORES',
            'dni' => 'required|integer|exists:Persona,dni',
            'idLista' => 'required|integer|exists:Lista,idLista',
        ]);

        $candidato = Candidato::create($request->all());

        return response()->json([
            'mensaje' => 'Candidato creado con éxito', 
            'candidato' => $candidato->load(['persona', 'lista'])
        ], 201);
    }

    // Mostrar un candidato específico
    public function show($id)
    {
        $candidato = Candidato::with(['persona', 'lista.provincia'])->findOrFail($id);
        return response()->json($candidato);
    }

    // Actualizar un candidato
    public function update(Request $request, $id)
    {
        $request->validate([
            'cargo' => 'sometimes|in:DIPUTADOS,SENADORES',
            'dni' => 'sometimes|integer|exists:Persona,dni',
            'idLista' => 'sometimes|integer|exists:Lista,idLista',
        ]);

        $candidato = Candidato::findOrFail($id);
        $candidato->update($request->all());

        return response()->json([
            'mensaje' => 'Candidato actualizado con éxito', 
            'candidato' => $candidato->load(['persona', 'lista'])
        ]);
    }

    // Eliminar un candidato
    public function destroy($id)
    {
        $candidato = Candidato::findOrFail($id);
        $candidato->delete();

        return response()->json(['mensaje' => 'Candidato eliminado correctamente']);
    }

    // Obtener candidatos por cargo (DIPUTADOS o SENADORES)
    public function porCargo($cargo)
    {
        if (!in_array(strtoupper($cargo), ['DIPUTADOS', 'SENADORES'])) {
            return response()->json(['error' => 'Cargo inválido. Use DIPUTADOS o SENADORES'], 400);
        }

        $candidatos = Candidato::where('cargo', strtoupper($cargo))
                                ->with(['persona', 'lista.provincia'])
                                ->get();
        return response()->json($candidatos);
    }

    // Obtener candidatos por lista
    public function porLista($idLista)
    {
        $candidatos = Candidato::where('idLista', $idLista)
                                ->with(['persona', 'lista.provincia'])
                                ->get();
        return response()->json($candidatos);
    }

    // Obtener candidatos por provincia
    public function porProvincia($idProvincia)
    {
        $candidatos = Candidato::whereHas('lista', function($query) use ($idProvincia) {
                                    $query->where('idProvincia', $idProvincia);
                                })
                                ->with(['persona', 'lista.provincia'])
                                ->get();
        return response()->json($candidatos);
    }

    // Obtener candidatos por provincia y cargo
    public function porProvinciaYCargo($idProvincia, $cargo)
    {
        if (!in_array(strtoupper($cargo), ['DIPUTADOS', 'SENADORES'])) {
            return response()->json(['error' => 'Cargo inválido. Use DIPUTADOS o SENADORES'], 400);
        }

        $candidatos = Candidato::whereHas('lista', function($query) use ($idProvincia) {
                                    $query->where('idProvincia', $idProvincia);
                                })
                                ->where('cargo', strtoupper($cargo))
                                ->with(['persona', 'lista.provincia'])
                                ->get();
        return response()->json($candidatos);
    }

    // Total de votos de un candidato (basado en su lista)
    public function totalVotos($idCandidato)
    {
        $candidato = Candidato::with(['persona', 'lista.provincia'])->findOrFail($idCandidato);
        
        // Los votos vienen de los resultados asociados a la lista del candidato
        $votos = $candidato->lista->resultados()->sum('votos');
        
        return response()->json([
            'candidato' => $candidato->persona->nombre ?? 'Sin nombre',
            'cargo' => $candidato->cargo,
            'lista' => $candidato->lista->nombre ?? 'Sin lista',
            'provincia' => $candidato->lista->provincia->nombre ?? 'Sin provincia',
            'total_votos' => $votos
        ]);
    }

    // Importar candidatos desde CSV/JSON
    public function importar(Request $request)
    {
        $request->validate([
            'candidatos' => 'required|array',
            'candidatos.*.idCandidato' => 'required|integer|unique:Candidato,idCandidato',
            'candidatos.*.cargo' => 'required|in:DIPUTADOS,SENADORES',
            'candidatos.*.dni' => 'required|integer|exists:Persona,dni',
            'candidatos.*.idLista' => 'required|integer|exists:Lista,idLista',
        ]);

        $candidatosCreados = [];
        foreach ($request->candidatos as $candidatoData) {
            $candidatosCreados[] = Candidato::create($candidatoData);
        }

        return response()->json([
            'mensaje' => 'Candidatos importados con éxito',
            'total' => count($candidatosCreados),
            'candidatos' => $candidatosCreados
        ], 201);
    }
}