<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $primaryKey = 'idUsuario';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = ['idUsuario', 'nombreDeUsuario', 'contrasenia', 'rol', 'dni'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'dni', 'dni');
    }

    public function telegramas()
    {
        return $this->hasMany(Telegrama::class, 'idUsuario', 'idUsuario');
    }
}

