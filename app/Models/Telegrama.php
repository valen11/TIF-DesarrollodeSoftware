<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telegrama extends Model
{
    protected $fillable = [
        'mesa_id', 'lista_id', 'votos_diputados', 'votos_senadores',
        'votos_blancos', 'votos_nulos', 'votos_recurridos',
        'usuario_carga', 'fecha_carga'
    ];
    
    protected $casts = [
        'fecha_carga' => 'datetime'
    ];
    
    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }
    
    public function lista()
    {
        return $this->belongsTo(Lista::class);
    }
}