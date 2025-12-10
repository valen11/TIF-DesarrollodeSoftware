<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    protected $fillable = ['lista_id', 'nombre_completo', 'orden_en_lista', 'observaciones'];
    
    public function lista()
    {
        return $this->belongsTo(Lista::class);
    }
    
    public function totalVotos()
    {
        $campo = $this->lista->cargo === 'DIPUTADOS' ? 'votos_diputados' : 'votos_senadores';
        return $this->lista->telegramas()->sum($campo);
    }
    
    public function porcentajeEnProvincia()
    {
        $totalVotos = $this->totalVotos();
        $totalProvincia = Telegrama::whereHas('mesa', function($query) {
            $query->where('provincia_id', $this->lista->provincia_id);
        })->sum($this->lista->cargo === 'DIPUTADOS' ? 'votos_diputados' : 'votos_senadores');
        
        return $totalProvincia > 0 ? ($totalVotos / $totalProvincia) * 100 : 0;
    }
}