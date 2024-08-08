<?php

use Sophy\Database\Drivers\PDODriver;
use Sophy\Database\DSN;
use SophyDB\SophyDB;

require_once __DIR__ . '/vendor/autoload.php';

Sophy\App::buildContainer();

SophyDB::addConn([
    'host' => 'localhost',
    'port' => '3306',

    'database' => 'masaris_control_activos',
    'username' => 'root',
    'password' => '',

    'charset' => DSN::UTF8,
    'collation' => DSN::UTF8_GENERAL_CI,
    'fetch' => PDODriver::FETCH_CLASS
]);

SophyDB::addConn([
    'host' => 'localhost',
    'port' => '3306',

    'database' => 'testudep',
    'username' => 'root',
    'password' => '',

    'charset' => DSN::UTF8,
    'collation' => DSN::UTF8_GENERAL_CI,
    'fetch' => PDODriver::FETCH_CLASS
], 'udep');

$allActivos = SophyDB::table('activo')->get();
$oneActivo = SophyDB::table('activo')->where('activo_nombre', 'HP Probook 440 G8')->first();
$oneActivoEmail = SophyDB::table('activo')->where('activo_nombre', 'HP Probook 440 G8')->value('activo_codigo');
$activoFound = SophyDB::table('activo')->find(212);
$names = SophyDB::table('activo')->pluck('activo_nombre', 'activo_codigo');

SophyDB::table('activo')->orderBy('activo_nombre')->chunk(100, function ($activos) use ($chunks) {
    foreach ($activos as $activo) {
        //
    }
});

SophyDB::table('activo')->orderBy('activo_nombre')->each(function ($activo) {
    $a = $activo;
});

$totalActivos = SophyDB::table('activo')->count();

$maxCode = SophyDB::table('activo')->max('activo_id');

$da = SophyDB::table('activo')->where('activo_id', 100000)->exists();

$das = SophyDB::table('activo')->where('activo_id', 100000)->doesntExist();

$activos = SophyDB::table('activo')
    ->cols('activo_nombre', 'activo_codigo')
    ->get();

$activos2 = SophyDB::table('activo')
    ->cols(['activo_nombre', 'activo_codigo'])
    ->get();

$avg = SophyDB::table('activo')
    ->where('activo_activo', 1)
    ->avg('activo_id');

$page = 1;

$list = SophyDB::table('activo')
    ->is('activo_activo')
    ->paginate(10, $page);

$activosGroup = SophyDB::table('activo')
    ->colsRaw('count(*) as activo_count, activo_activo')
    ->where('activo_activo', 1)
    ->groupBy('activo_nombre')
    ->get();

$activosOthers = SophyDB::table('activo')
    ->cols(function ($query) {
        $query->count('*')->as('activo_count');
        $query->field('activo_activo');
    })
    ->get();

$activosOthers2 = SophyDB::table('activo')
    ->whereRaw('activo_costo > IF(activo_activo = "1  ", ?, 100)', [200])
    ->get();

$activosOthers3 = SophyDB::table('activo')
    ->cols('activo_nombre', SophyDB::colsRaw('SUM(activo_costo) as total_sales'))
    ->groupBy('activo_nombre')
    ->havingRaw('SUM(activo_costo) > ?', [2500])
    ->get();

$activos1 = SophyDB::table('activo')
    ->join('activogeneral', 'activo.activogeneral_id', '=', 'activogeneral.activogeneral_id')
    ->cols('activo.*', 'activogeneral.activogeneral_modelo', 'activogeneral.activogeneral_costo')
    ->get();

$activos3 = SophyDB::table('activo')
    ->join('activogeneral.activogeneral_id', 'activo.activogeneral_id')
    ->cols('activo.*', 'activogeneral.activogeneral_id')
    ->get();

$activos4 = SophyDB::table('activo')
    ->leftJoin('activogeneral', 'activo.activo_id', '=', 'activogeneral.activogeneral_id')
    ->get();

$activos5 = SophyDB::table('activo')
    ->rightJoin('activogeneral', 'activo.activo_id', '=', 'activogeneral.activogeneral_id')
    ->get();

$activos6 = SophyDB::table('activo')
    ->where('activo_activo', '=', 1)
    ->where('activo_costo', '>', 100)
    ->get();

$activos7 = SophyDB::table('activo')->where('activo_codigo', 'Lauretta.Kohler@gmail.com')->get();

$activos8 = SophyDB::table('activo')
    ->where('activo_costo', '>=', 32)
    ->get();

$activos9 = SophyDB::table('activo')
    ->where('activo_costo', '<>', 32)
    ->get();

$activos10 = SophyDB::table('activo')
    ->where('activo_nombre', 'like', 'Ro%')
    ->get();

$activos11 = SophyDB::table('activo')
    ->where('activo_costo', '=', 4)
    ->orWhere('activo_costo', '100')
    ->get();

$activos12 = SophyDB::table('activo')
    ->where('activo_costo', '>', 5)
    ->orWhere(function ($query) {
        $query->where('activo_costo', '100')
            ->and('activo_costo', '>', 4)
            ->and('activo_costo', '>', 5)
            ->where('activo_costo', '>', 6)
            ->where('activo_costo', '>', 7)
            ->where('activo_costo', '>', 8)
            ->where('activo_costo', '>', 9);
    })
    ->get();

$activos13 = SophyDB::table('activo')
    ->whereNot(function ($query) {
        $query->where('activo_activo', 1)
            ->orWhere('activo_costo', '<', 180);
    })
    ->get();

$activos14 = SophyDB::table('activo')
    ->whereBetween('activo_costo', [180, 500])
    ->get();

$activos15 = SophyDB::table('activo')
    ->whereNotBetween('activo_costo', [180, 500])
    ->get();

$activos16 = SophyDB::table('activo')
    ->whereIn('activo_id', [212, 215, 219])
    ->get();

$activos17 = SophyDB::table('activo')
    ->whereNotIn('activo_id', [212, 215, 219])
    ->get();

$activos18 = SophyDB::table('activo')
    ->whereNull('contrato_id')
    ->get();

$activos19 = SophyDB::table('activo')
    ->whereNotNull('contrato_id')
    ->get();

$activos20 = SophyDB::table('activo')
    ->whereDate('activo_createdAt', '2016-12-31')
    ->get();

$activos21 = SophyDB::table('activo')
    ->whereMonth('activo_createdAt', '12')
    ->get();

$activos22 = SophyDB::table('activo')
    ->whereDay('activo_createdAt', '31')
    ->get();

$activos23 = SophyDB::table('activo')
    ->whereYear('activo_createdAt', '2016')
    ->get();

$activos24 = SophyDB::table('activo')
    ->whereTime('activo_createdAt', '=', '11:20:45')
    ->get();

$activos25 = SophyDB::table('activogeneral')
    ->whereColumn('categoria_id', 'moneda_id')
    ->get();

$activos26 = SophyDB::table('activogeneral')
    ->whereColumn('marca_id', '>', 'moneda_id')
    ->get();

$activos27 = SophyDB::table('activogeneral')
    ->orderBy('activogeneral_id', 'desc')
    ->get();

$activos28 = SophyDB::table('activogeneral')
    ->orderBy('activogeneral_id', 'asc')
    //->orderBy('categoria_id', 'desc')
    ->get();

$activos29 = SophyDB::table('activo')
    ->latest('activo_createdAt')
    ->first();

$activos30 = SophyDB::table('activo')
    ->inRandomOrder()
    ->first();

$activos31 = SophyDB::table('activo')
    ->groupBy('categoria_id')
    ->having('activo_costo', '>', 180)
    ->get();

$activos32 = SophyDB::table('activo')
    ->groupBy('categoria_id', 'estadoactivo_id')
    ->having('activo_costo', '>', 200)
    ->get();

$activos33 = SophyDB::table('activo')->skip(10)->take(5)->get();

$activos34 = SophyDB::table('activo')
    ->offset(10)
    ->limit(5)
    ->get();

$activos35 = SophyDB::table('activo')->is('activo_activo')->get();
$activos36 = SophyDB::table('activo')->true('activo_activo')->get();
$activos37 = SophyDB::table('activo')->is('activo_activo', false)->get();
$activos38 = SophyDB::table('activo')->false('activo_activo')->get();

$activos39 = SophyDB::table('activo')
    ->is('activo_activo')
    ->and('activo_costo', '>', 180)
    ->or('categoria_id', 2)
    ->get();

$activos40 = SophyDB::table('activo')
    ->in('activo_id', [212, 216, 219])
    ->get();

SophyDB::table('area')->insert([
    'area_nombre' => 'pedro',
    'area_activa' => 1
]);

$id = SophyDB::table('area')->insertGetId(
    ['area_nombre' => 'michael', 'area_activa' => 0]
);

$affected = SophyDB::table('area')
    ->where('area_id', 10)
    ->update(['area_activa' => 1]);

SophyDB::table('moneda')->where('moneda_id', 1)->increment('moneda_tipocambio');

SophyDB::table('moneda')->where('moneda_id', 1)->increment('moneda_tipocambio', 1);

SophyDB::table('moneda')->where('moneda_id', 1)->decrement('moneda_tipocambio');

SophyDB::table('moneda')->where('moneda_id', 1)->decrement('moneda_tipocambio', 1);

SophyDB::table('area')->where('area_id', 20)->delete();
SophyDB::table('area')->in('area_id', [3, 4, 5, 6, 7, 8, 9, 10])->delete();

//Cambio de BD
SophyDB::use('udep');

$allAreas = SophyDB::table('area')->get();

$a = 1;
