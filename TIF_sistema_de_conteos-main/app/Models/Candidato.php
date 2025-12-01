<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    use HasFactory;

    protected $primaryKey = 'idCandidato';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = ['idCandidato', 'dni', 'cargo', 'ordenEnLista', 'nombre', 'apellido', 'idLista'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'dni', 'dni');
    }

    public function lista()
    {
        return $this->belongsTo(Lista::class, 'idLista', 'idLista');
    }
}

