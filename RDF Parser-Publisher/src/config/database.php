<?php
//require_once '../../vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();
/*
$capsule->addConnection(array(
	'driver'    => 'mysql',
    'host'      => '',
    'database'  => '',
    'username'  => '',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => ''
	));
*/

//local Ntua credentials
$capsule->addConnection(array(
	'driver'    => 'mysql',
    'host'      => 'localhost',
    ////from VPN
    //'host'      =>  '',
    'database'  => '',
    'username'  => '',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => ''
	));


//to use with static methods
$capsule->setAsGlobal();
$capsule->bootEloquent();

//date_default_timezone_set('UTC');
