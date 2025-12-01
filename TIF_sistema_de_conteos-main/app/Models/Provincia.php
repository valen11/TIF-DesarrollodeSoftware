<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    use HasFactory;

    protected $primaryKey = 'idProvincia';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = ['idProvincia', 'nombre'];

    public function listas()
    {
        return $this->hasMany(Lista::class, 'idProvincia');
    }

    public function mesas()
    {
        return $this->hasMany(Mesa::class, 'idProvincia');
    }
}
