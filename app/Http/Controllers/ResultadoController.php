<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Models\Lista;
use App\Models\Candidato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultadoController extends Controller
{
    public function porProvincia($provincia_id)
    {
        $provincia = Provincia::findOrFail($provincia_id);
        
        // Resultados Diputados
        $diputados = DB::table('telegramas')
            ->join('listas', 'telegramas.lista_id', '=', 'listas.id')
            ->join('mesas', 'telegramas.mesa_id', '=', 'mesas.id')
            ->where('mesas.provincia_id', $provincia_id)
            ->where('listas.cargo', 'DIPUTADOS')
            ->select(
                'listas.id',
                'listas.nombre',
                'listas.alianza',
                DB::raw('SUM(telegramas.votos_diputados) as total_votos')
            )
            ->groupBy('listas.id', 'listas.nombre', 'listas.alianza')
            ->orderByDesc('total_votos')
            ->get();
        
        $total_votos_dip = $diputados->sum('total_votos');
        
        $diputados = $diputados->map(function($item) use ($total_votos_dip) {
            $item->porcentaje = $total_votos_dip > 0 ? ($item->total_votos / $total_votos_dip) * 100 : 0;
            return $item;
        });
        
        // Resultados Senadores
        $senadores = DB::table('telegramas')
            ->join('listas', 'telegramas.lista_id', '=', 'listas.id')
            ->join('mesas', 'telegramas.mesa_id', '=', 'mesas.id')
            ->where('mesas.provincia_id', $provincia_id)
            ->where('listas.cargo', 'SENADORES')
            ->select(
                'listas.id',
                'listas.nombre',
                'listas.alianza',
                DB::raw('SUM(telegramas.votos_senadores) as total_votos')
            )
            ->groupBy('listas.id', 'listas.nombre', 'listas.alianza')
            ->orderByDesc('total_votos')
            ->get();
        
        $total_votos_sen = $senadores->sum('total_votos');
        
        $senadores = $senadores->map(function($item) use ($total_votos_sen) {
            $item->porcentaje = $total_votos_sen > 0 ? ($item->total_votos / $total_votos_sen) * 100 : 0;
            return $item;
        });
        
        // Participación
        $total_electores = $provincia->mesas()->sum('electores');
        $participacion_dip = $total_electores > 0 ? ($total_votos_dip / $total_electores) * 100 : 0;
        $participacion_sen = $total_electores > 0 ? ($total_votos_sen / $total_electores) * 100 : 0;
        
        return view('resultados.provincia', compact(
            'provincia', 'diputados', 'senadores',
            'total_votos_dip', 'total_votos_sen',
            'participacion_dip', 'participacion_sen'
        ));
    }
    
    public function nacional()
    {
        // Agregado nacional Diputados
        $diputados = DB::table('telegramas')
            ->join('listas', 'telegramas.lista_id', '=', 'listas.id')
            ->where('listas.cargo', 'DIPUTADOS')
            ->select(
                'listas.alianza',
                DB::raw('SUM(telegramas.votos_diputados) as total_votos')
            )
            ->groupBy('listas.alianza')
            ->orderByDesc('total_votos')
            ->get();
        
        $total_votos_dip = $diputados->sum('total_votos');
        
        $diputados = $diputados->map(function($item) use ($total_votos_dip) {
            $item->porcentaje = $total_votos_dip > 0 ? ($item->total_votos / $total_votos_dip) * 100 : 0;
            return $item;
        });
        
        // Agregado nacional Senadores
        $senadores = DB::table('telegramas')
            ->join('listas', 'telegramas.lista_id', '=', 'listas.id')
            ->where('listas.cargo', 'SENADORES')
            ->select(
                'listas.alianza',
                DB::raw('SUM(telegramas.votos_senadores) as total_votos')
            )
            ->groupBy('listas.alianza')
            ->orderByDesc('total_votos')
            ->get();
        
        $total_votos_sen = $senadores->sum('total_votos');
        
        $senadores = $senadores->map(function($item) use ($total_votos_sen) {
            $item->porcentaje = $total_votos_sen > 0 ? ($item->total_votos / $total_votos_sen) * 100 : 0;
            return $item;
        });
        
        // Participación nacional
        $total_electores = Mesa::sum('electores');
        $participacion = $total_electores > 0 ? (($total_votos_dip + $total_votos_sen) / ($total_electores * 2)) * 100 : 0;
        
        return view('resultados.nacional', compact(
            'diputados', 'senadores',
            'total_votos_dip', 'total_votos_sen',
            'participacion'
        ));
    }
    
    public function exportarProvincia($provincia_id)
    {
        $provincia = Provincia::findOrFail($provincia_id);
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=resultados_{$provincia->nombre}.csv"
        ];
        
        $callback = function() use ($provincia_id) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Cargo', 'Lista', 'Alianza', 'Votos', 'Porcentaje']);
            
            // Diputados
            $diputados = DB::table('telegramas')
                ->join('listas', 'telegramas.lista_id', '=', 'listas.id')
                ->join('mesas', 'telegramas.mesa_id', '=', 'mesas.id')
                ->where('mesas.provincia_id', $provincia_id)
                ->where('listas.cargo', 'DIPUTADOS')
                ->select('listas.nombre', 'listas.alianza', DB::raw('SUM(telegramas.votos_diputados) as total'))
                ->groupBy('listas.id', 'listas.nombre', 'listas.alianza')
                ->get();
            
            $total_dip = $diputados->sum('total');
            foreach ($diputados as $item) {
                $porc = $total_dip > 0 ? ($item->total / $total_dip) * 100 : 0;
                fputcsv($file, ['DIPUTADOS', $item->nombre, $item->alianza, $item->total, number_format($porc, 2)]);
            }
            
            // Senadores
            $senadores = DB::table('telegramas')
                ->join('listas', 'telegramas.lista_id', '=', 'listas.id')
                ->join('mesas', 'telegramas.mesa_id', '=', 'mesas.id')
                ->where('mesas.provincia_id', $provincia_id)
                ->where('listas.cargo', 'SENADORES')
                ->select('listas.nombre', 'listas.alianza', DB::raw('SUM(telegramas.votos_senadores) as total'))
                ->groupBy('listas.id', 'listas.nombre', 'listas.alianza')
                ->get();
            
            $total_sen = $senadores->sum('total');
            foreach ($senadores as $item) {
                $porc = $total_sen > 0 ? ($item->total / $total_sen) * 100 : 0;
                fputcsv($file, ['SENADORES', $item->nombre, $item->alianza, $item->total, number_format($porc, 2)]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}