<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $primaryKey = 'dni';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = ['dni', 'nombre', 'apellido'];

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'dni', 'dni');
    }

    public function candidatos()
    {
        return $this->hasMany(Candidato::class, 'dni', 'dni');
    }
}
