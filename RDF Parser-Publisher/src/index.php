<?php
//require '../vendor/autoload.php';
//require_once '../vendor/autoload.php';
require_once 'start.php';
//require_once 'config/database.php';
//use Sot\optimus\Importer\HttpRequester;


//$app = new Illuminate\Container\Container;

//$foo = $app->make('Sot\\optimus\\Importer\\HttpRequester');
//$bar = $app->make('Sot\\optimus\\Importer\\TridiumImporter');

print_r($TridiumImporter);
echo "\n***\n";
print_r($RDFGenerator);
echo "\n***\n";
print_r($HttpRequester);
echo "\n***\n";



//$data_importer = $DataImporter;
//$data_importer->city = "Athens";
//print_r($data_importer);
echo "\n***\n";

//$mapping = new MappingOp;
//$mappings = $mapping->index();
//print_r($mappings);


$modules = new Module;
//$mappings = $modules->index();
//print_r($mappings);

//$roles = App\User::find(1)->roles()->orderBy('name')->get();


//$modulesCsv = $modules->find(2)->mappingops()->get();
//$modulesCsv = $modules::where('name','CSV')->get()->toArray();



//print_r($modulesCsv);




echo "\n***1\n";

//$users = App\User::ofType('admin')->get()
$modulesCsv2 = $modules::ofType('XML')->get();
//var_dump($modulesCsv2);
echo "\n***2\n";

$prevModule = 0;
$headers = array();

//$headers = array("siemens_indoortemperature","siemens_mainenergyconsumption");
//print_r($headers);
//die;

foreach ($modulesCsv2 as $model) {
	echo "bio2=".$model->password."\n";
	//$city = $model->site->getAttribute('city');
	$city = $model->site->city;


	echo "\nbio = ".$city."\n";
	echo "\n*7*&*&*\n";
	/*$mappingopsTmp = $model->mappingops;

	foreach ($mappingopsTmp as $key) {
		$headers[$key->getAttribute('measure')] = array('stream'=>$key->getAttribute('stream'),
														'sensor'=>$key->getAttribute('sensor'),
														'units'=>$key->getAttribute('units')
												);
		//print_r($key->getAttributes());
		//print_r($mappingopsTmp);
	}

	print_r($headers);*/

	echo "\n******\n";


}



/*
foreach ($modulesCsv2 as $model) {
	$mappingopsTmp = $model->mappingops->toArray();
	print_r($mappingopsTmp);

	echo "\n******\n";
	for($i=0; $i <count($mappingopsTmp); $i++){
		$headers[$mappingopsTmp[$i]['measure']] = array('stream'=>$mappingopsTmp[$i]['stream'],
														"sensor"=>$mappingopsTmp[$i]['sensor'],
														"units"=>$mappingopsTmp[$i]['units']);
	}

	print_r($headers);
	//$headers[] = $mappingopsTmp['stream'];

}

 */

//print_r($headers);


die;
//$headers = $modules::where('name', 'CSV')->mappingops()->get();


//$user = App\User::find(1);

///$user->posts()->where('active', 1)->get();

//print_r($modulesCsv->toArray());



//$comments = App\Post::find(1)->comments()->where('title', 'foo')->first();





echo "\n***\n";
//print_r($modulesCsv);
/**
 * [$MappingOp get Mappings]
 * @var MappingOp
 */
//$MappingOp = new MappingOp();
//$MappingOps = $MappingOp->index();
//print_r($MappingOps);

//foreach ($variable as $key => $value) {
	# code...
//}


//$site = Site::find(1)->city;
//echo "site = ".$site;
//Site::create(['city'=>'San Francisco']);


// ['timestamp','triple','stream','city','sensor'];
//
//$MappingOp = MappingOp::all();
//print_r($MappingOp->toArray());
/*


 inser data
$data = array(
    array('timestamp'=>'2015-05-10 09:00:00','triple'=>'triple example1','stream'=>'stream example1','city'=>'LA','sensor'=>'humidity sensor'),
    array('timestamp'=>'2015-05-10 09:01:00','triple'=>'triple example2','stream'=>'stream example2','city'=>'LA','sensor'=>'humidity sensor'),
    array('timestamp'=>'2015-05-10 09:02:00','triple'=>'triple example3','stream'=>'stream example3','city'=>'LA','sensor'=>'humidity sensor'),
    array('timestamp'=>'2015-05-10 09:03:00','triple'=>'triple example4','stream'=>'stream example4','city'=>'LA','sensor'=>'humidity sensor'),
    array('timestamp'=>'2015-05-10 09:04:00','triple'=>'triple example5','stream'=>'stream example5','city'=>'LA','sensor'=>'humidity sensor'),
    //...
);

Triple::insert($data);*/

//Triple::create(['timestamp'=>'2015-05-10 09:00:00','triple'=>'triple example','stream'=>'stream example','city'=>'LA','sensor'=>'humidity sensor']);

//$site->city = "kkot";
//echo $site->city;
//$site = new Site;
//$gio = $site->find(1);
//print_r($gio);

//$mapping = MappingOp::find(1);
//$modules = $mapping->module;

//var_dump($modules);


//var_dump($foo);
//var_dump($bar);


//$site = $app->make('Sot\\optimus\\Models\\Site');
//var_dump($app);

//$app->bindShared('car', function () {
//    return new JeepWrangler;
//});
