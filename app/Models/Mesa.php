<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    protected $fillable = ['numero_mesa', 'provincia_id', 'circuito', 'establecimiento', 'electores'];
    
    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }
    
    public function telegramas()
    {
        return $this->hasMany(Telegrama::class);
    }
    
    public function validarTotales()
    {
        $totales = $this->telegramas()
            ->selectRaw('
                SUM(votos_diputados) as total_dip,
                SUM(votos_senadores) as total_sen,
                MAX(votos_blancos) as blancos,
                MAX(votos_nulos) as nulos,
                MAX(votos_recurridos) as recurridos
            ')
            ->first();
        
        $total_dip = $totales->total_dip + $totales->blancos + $totales->nulos + $totales->recurridos;
        $total_sen = $totales->total_sen + $totales->blancos + $totales->nulos + $totales->recurridos;
        
        return [
            'valido' => $total_dip <= $this->electores && $total_sen <= $this->electores,
            'total_diputados' => $total_dip,
            'total_senadores' => $total_sen,
            'electores' => $this->electores
        ];
    }
}