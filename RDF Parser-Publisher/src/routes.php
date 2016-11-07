<?php
//namespace Sot\optimus;
require '../vendor/autoload.php';

//use Sot\optimus\Importer\HttpRequester;

$app = new Illuminate\Container\Container;

$foo = $app->make('Sot\\optimus\\tools\\HttpRequester');
$bar = $app->make('Sot\\optimus\\Importer\\TridiumImporter');



//$site = new Sot\optimus\Models\Site;
$site = new Site;
$gio = $site->find(1);
print_r($gio);

//var_dump($foo);
//var_dump($bar);


//$site = $app->make('Sot\\optimus\\Models\\Site');
//var_dump($app);

//$app->bindShared('car', function () {
//    return new JeepWrangler;
//});
