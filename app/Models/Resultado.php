<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    use HasFactory;

    protected $primaryKey = 'idResultado';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = ['idResultado', 'votos', 'porcentaje', 'idLista', 'idTelegrama'];

    public function lista()
    {
        return $this->belongsTo(Lista::class, 'idLista', 'idLista');
    }

    public function telegrama()
    {
        return $this->belongsTo(Telegrama::class, 'idTelegrama', 'idTelegrama');
    }
}

