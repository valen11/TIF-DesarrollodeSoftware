<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partidos extends Model
{
    use HasFactory;

    protected $table = 'partidos';

    protected $fillable = [
        'nombre',
        'siglas',
        'numero_lista',
        'presidente_nombre',
        'color_hex',
        'activo',
        'fundacion_fecha',
    ];
}
