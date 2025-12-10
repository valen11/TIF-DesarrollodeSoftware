<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $fillable = ['nombre', 'bancas_diputados', 'bancas_senadores', 'umbral_dhondt'];
    
    public function listas()
    {
        return $this->hasMany(Lista::class);
    }
    
    public function mesas()
    {
        return $this->hasMany(Mesa::class);
    }
    
    public function resultadosDiputados()
    {
        return DB::table('telegramas')
            ->join('listas', 'telegramas.lista_id', '=', 'listas.id')
            ->join('mesas', 'telegramas.mesa_id', '=', 'mesas.id')
            ->where('mesas.provincia_id', $this->id)
            ->where('listas.cargo', 'DIPUTADOS')
            ->select('listas.nombre', 'listas.alianza', DB::raw('SUM(telegramas.votos_diputados) as total_votos'))
            ->groupBy('listas.id', 'listas.nombre', 'listas.alianza')
            ->get();
    }
}