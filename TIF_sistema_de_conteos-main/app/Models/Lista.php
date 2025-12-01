<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{
    use HasFactory;

    protected $primaryKey = 'idLista';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = ['idLista', 'nombre', 'alianza', 'cargoDiputado', 'cargoSenador', 'idProvincia'];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'idProvincia', 'idProvincia');
    }

    public function candidatos()
    {
        return $this->hasMany(Candidato::class, 'idLista', 'idLista');
    }

    public function resultados()
    {
        return $this->hasMany(Resultado::class, 'idLista', 'idLista');
    }
}

