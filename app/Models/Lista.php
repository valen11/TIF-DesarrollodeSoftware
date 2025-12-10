<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{
    protected $fillable = ['provincia_id', 'cargo', 'nombre', 'alianza'];
    
    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }
    
    public function candidatos()
    {
        return $this->hasMany(Candidato::class)->orderBy('orden_en_lista');
    }
    
    public function telegramas()
    {
        return $this->hasMany(Telegrama::class);
    }
    
    public function totalVotos()
    {
        $campo = $this->cargo === 'DIPUTADOS' ? 'votos_diputados' : 'votos_senadores';
        return $this->telegramas()->sum($campo);
    }
}