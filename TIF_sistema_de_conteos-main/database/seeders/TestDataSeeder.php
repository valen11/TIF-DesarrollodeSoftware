<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Provincia;
use App\Models\Lista;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Mesa;
use App\Models\Telegrama;
use App\Models\Resultado;
use App\Models\Candidato;


class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // PROVINCIAS
$prov1 = Provincia::create(['idProvincia' => 1, 'nombre' => 'Buenos Aires']);
$prov2 = Provincia::create(['idProvincia' => 2, 'nombre' => 'Córdoba']);
$prov3 = Provincia::create(['idProvincia' => 3, 'nombre' => 'Mendoza']);


// PERSONAS
$pers1 = Persona::create(['dni' => '10000001', 'nombre' => 'Juan', 'apellido' => 'Pérez']);
$pers2 = Persona::create(['dni' => '10000002', 'nombre' => 'María', 'apellido' => 'Gómez']);
$pers3 = Persona::create(['dni' => '10000003', 'nombre' => 'Lucía', 'apellido' => 'Fernández']);


// USUARIOS
$usu1 = Usuario::create([
    'idUsuario' => 1,
    'nombreDeUsuario' => 'juan_admin',
    'contrasenia' => '1234',
    'rol' => 'admin',
    'dni' => $pers1->dni
]);

$usu2 = Usuario::create([
    'idUsuario' => 2,
    'nombreDeUsuario' => 'maria_op',
    'contrasenia' => 'abcd',
    'rol' => 'operador',
    'dni' => $pers2->dni
]);

$usu3 = Usuario::create([
    'idUsuario' => 3,
    'nombreDeUsuario' => 'lucia_super',
    'contrasenia' => 'pass',
    'rol' => 'supervisor',
    'dni' => $pers3->dni
]);


// LISTAS
$lista1 = Lista::create([
    'idLista' => 1,
    'nombre' => 'Fuerza Popular',
    'alianza' => 'Alianza Azul',
    'cargoDiputado' => 'Dip. Nacional',
    'cargoSenador' => 'Sen. Nacional',
    'idProvincia' => $prov1->idProvincia
]);

$lista2 = Lista::create([
    'idLista' => 2,
    'nombre' => 'Unidad Federal',
    'alianza' => 'Frente Rojo',
    'cargoDiputado' => 'Dip. Provincial',
    'cargoSenador' => 'Sen. Provincial',
    'idProvincia' => $prov2->idProvincia
]);

$lista3 = Lista::create([
    'idLista' => 3,
    'nombre' => 'Movimiento Verde',
    'alianza' => 'Alianza Ambiental',
    'cargoDiputado' => 'Dip. Municipal',
    'cargoSenador' => 'Sen. Municipal',
    'idProvincia' => $prov3->idProvincia
]);


// MESAS
$mesa1 = Mesa::create([
    'idMesa' => 1,
    'electores' => 180,
    'establecimiento' => 'Escuela N°12',
    'circuito' => 'Circ 1A',
    'idProvincia' => $prov1->idProvincia
]);

$mesa2 = Mesa::create([
    'idMesa' => 2,
    'electores' => 220,
    'establecimiento' => 'Colegio San Martín',
    'circuito' => 'Circ 2B',
    'idProvincia' => $prov2->idProvincia
]);

$mesa3 = Mesa::create([
    'idMesa' => 3,
    'electores' => 160,
    'establecimiento' => 'Esc. República de Italia',
    'circuito' => 'Circ 3C',
    'idProvincia' => $prov3->idProvincia
]);


// TELEGRAMAS
$tel1 = Telegrama::create([
    'idTelegrama' => 1,
    'votosDiputados' => 85,
    'votosSenadores' => 70,
    'blancos' => 4,
    'nulos' => 2,
    'impugnados' => 1,
    'fechaHora' => now(),
    'idMesa' => $mesa1->idMesa,
    'idUsuario' => $usu1->idUsuario
]);

$tel2 = Telegrama::create([
    'idTelegrama' => 2,
    'votosDiputados' => 130,
    'votosSenadores' => 110,
    'blancos' => 7,
    'nulos' => 5,
    'impugnados' => 3,
    'fechaHora' => now(),
    'idMesa' => $mesa2->idMesa,
    'idUsuario' => $usu2->idUsuario
]);

$tel3 = Telegrama::create([
    'idTelegrama' => 3,
    'votosDiputados' => 95,
    'votosSenadores' => 88,
    'blancos' => 6,
    'nulos' => 3,
    'impugnados' => 2,
    'fechaHora' => now(),
    'idMesa' => $mesa3->idMesa,
    'idUsuario' => $usu3->idUsuario
]);


// RESULTADOS
Resultado::create([
    'idResultado' => 1,
    'votos' => 85,
    'porcentaje' => 47.22,
    'idLista' => $lista1->idLista,
    'idTelegrama' => $tel1->idTelegrama
]);

Resultado::create([
    'idResultado' => 2,
    'votos' => 130,
    'porcentaje' => 59.09,
    'idLista' => $lista2->idLista,
    'idTelegrama' => $tel2->idTelegrama
]);

Resultado::create([
    'idResultado' => 3,
    'votos' => 95,
    'porcentaje' => 52.78,
    'idLista' => $lista3->idLista,
    'idTelegrama' => $tel3->idTelegrama
]);


// CANDIDATOS
Candidato::create([
    'idCandidato' => 1,
    'dni' => $pers1->dni,
    'cargo' => 'Diputado',
    'ordenEnLista' => 1,
    'nombre' => $pers1->nombre,
    'apellido' => $pers1->apellido,
    'idLista' => $lista1->idLista
]);

Candidato::create([
    'idCandidato' => 2,
    'dni' => $pers2->dni,
    'cargo' => 'Senadora',
    'ordenEnLista' => 1,
    'nombre' => $pers2->nombre,
    'apellido' => $pers2->apellido,
    'idLista' => $lista2->idLista
]);

Candidato::create([
    'idCandidato' => 3,
    'dni' => $pers3->dni,
    'cargo' => 'Diputada',
    'ordenEnLista' => 2,
    'nombre' => $pers3->nombre,
    'apellido' => $pers3->apellido,
    'idLista' => $lista3->idLista
]);

    }
}
