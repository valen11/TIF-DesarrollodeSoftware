<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partidos;

class partidocontroller extends Controller
{
    // If you want to keep only the JSON response, remove the first index method
    public function index()
    {
        $partidos = Partidos::all();
        
        if ($partidos->isEmpty()) {
            $data = [
                'status' => 404,
                'message' => 'No se encontraron partidos políticos.',
                'status_code' => 404
            ];
            return response()->json($data, 404);
        }
        return response()->json($partidos, 200);
    }
};
