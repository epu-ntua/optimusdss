<?php

/* 
 * Giannis Tsapelas 2015
 */

require_once '../classes/CitySubmission.php';
require_once '../classes/City.php';
require_once '../classes/Building.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once("../includes/config.php");
require_once("../includes/database.php");


$_SESSION['mode'] = 'edit';

//*** Get the Submission ID ***
$sid = $_GET['sid'];

/*
** Create a new City and a new City Submission objects
*/ 
$_SESSION[$_GET['city']] = new City();
$_SESSION[$_GET['citySubmission']] = new CitySubmission();

$City = $_SESSION[$_GET['city']];

$CitySubmission = $_SESSION[$_GET['citySubmission']];

$CitySubmission->setId($sid);

// Get general data for the submission
$sql = "SELECT submission.*, city.name, city.country, submission.baseline, submission.name as namme "
     . "FROM submission "
     . "INNER JOIN city "
     . "ON submission.city_id = city.id "
     . "WHERE submission.id=".$sid;

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

while($row = mysql_fetch_array($sql)){
    $City->setCityName($row['name']);
    $City->setCountry($row['country']);
    $City->setId($row['city_id']);
    
    $CitySubmission->setYear($row['year']);
    $CitySubmission->setVisibility($row['visibility']);
    $CitySubmission->setBaseline($row['baseline']);
    $CitySubmission->setName($row['namme']);
}

//Get the targets of the submission
$sql = "SELECT * "
     . "FROM targets "
     . "WHERE city_id=".$City->getId();

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

$targets = array("consumption"=> null, "emissions"=> null, "cost"=> null, "res"=> null);
while($row = mysql_fetch_array($sql)){
    $targets['consumption'] = $row['consumption'];
    $targets['emissions'] = $row['emissions'];
    $targets['cost'] = $row['cost'];
    $targets['res'] = $row['res'];
    $targetYear = $row['year'];
}
$City->setTargets($targets);
$CitySubmission->setTargetYear($targetYear);


// Get the Emission Factors
$sql = "SELECT * "
     . "FROM emissionfactors "
     . "WHERE submission_id=".$sid;

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

$factors = array("electricity"=> null, "fuel"=> null, "naturalGas"=> null, "other"=> null);
while($row = mysql_fetch_array($sql)){
    $factors['electricity'] = $row['electricity'];
    $factors['fuel'] = $row['fuel'];
    $factors['naturalGas'] = $row['naturalGas'];
    $factors['other'] = $row['other'];
}
$CitySubmission->setFactors($factors);


// Get all the buildings in submission included
$sql = "SELECT building.* "
     . "FROM building "
     . "INNER JOIN building_consumption "
     . "ON building.id = building_consumption.building_id "
     . "WHERE building_consumption.submission_id=".$sid." "
     . "AND building_consumption.included=1";

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

$CitySubmission->setBuildings(array());
while($row = mysql_fetch_array($sql)){
    $building = new Building();
    $building->setId($row['id']);
    $building->setName($row['name']);
    $building->setType($row['type']);
    $building->setStatus("included");
    $CitySubmission->addBuilding($building);
}


// Get all the buildings in submission not included
$sql = "SELECT building.* "
     . "FROM building "
     . "INNER JOIN building_consumption "
     . "ON building.id = building_consumption.building_id "
     . "WHERE building_consumption.submission_id=".$sid." "
     . "AND building_consumption.included=0";

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

while($row = mysql_fetch_array($sql)){
    $building = new Building();
    $building->setId($row['id']);
    $building->setName($row['name']);
    $building->setType($row['type']);
    $building->setStatus("excluded_data");
    $CitySubmission->addBuilding($building);
}

$buildingList = $CitySubmission->getBuildings();


//Get the buildings' consumptions
$sql = "SELECT * "
     . "FROM building_consumption "
     . "WHERE submission_id=".$sid;

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

while($row = mysql_fetch_array($sql)){
    foreach ($buildingList as $building) {
        if($building->getId()== $row['building_id']){
            $consumption = array("heating"=> $row['heating'], "cooling"=> $row['cooling'], "other"=> $row['other']);
            $building->setConsumption($consumption);
            break;
        }
    }
}

//Get the buildings' BASELINE consumptions
$sql = "SELECT id "
     . "FROM submission "
     . "WHERE city_id = '" . $_SESSION['City']->getId(). "' AND user_id = '" . $_SESSION['user'] . "' AND year = '" . $_SESSION['CitySubmission']->getBaseline() . "'";

if(!$sql = mysql_query($sql, $database->connection)){
   die("Database connection failed:".mysql_error()); 
   //die('Ooops! We encountered a database error');
}

$baselineID = null;
while($row = mysql_fetch_array($sql)){
   $baselineID = $row['id'];
}

$sql = "SELECT * "
     . "FROM building_consumption "
     . "WHERE submission_id=".$baselineID;

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

while($row = mysql_fetch_array($sql)){
    foreach ($buildingList as $building) {
        if($building->getId()== $row['building_id']){
            $consumption = array("heating"=> $row['heating'], "cooling"=> $row['cooling'], "other"=> $row['other']);
            $building->setConsumptionBaseline($consumption);
            break;
        }
    }
}


// Get the buildings' production
$sql = "SELECT * "
     . "FROM building_production "
     . "WHERE submission_id=".$sid;

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

while($row = mysql_fetch_array($sql)){
    foreach ($buildingList as $building) {
        if($building->getId()== $row['building_id']){
            $building->setProduction($row["production"]);
            break;
        }
    }
}

// Get the buildings' BASELINE production
$sql = "SELECT * "
     . "FROM building_production "
     . "WHERE submission_id=".$baselineID;

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

while($row = mysql_fetch_array($sql)){
    foreach ($buildingList as $building) {
        if($building->getId()== $row['building_id']){
            $building->setProductionBaseline($row["production"]);
            break;
        }
    }
}


// Get the prices
$sql = "SELECT * "
     . "FROM building_prices "
     . "WHERE submission_id=".$sid;

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

$prices = array("electricity"=> null, "fuel"=> null, "naturalGas"=> null, "other"=> null);
while($row = mysql_fetch_array($sql)){
    foreach ($buildingList as $building) {
        if($building->getId()== $row['building_id']){
            $prices['electricity'] = $row['electricity'];
            $prices['fuel'] = $row['fuel'];
            $prices['naturalGas'] = $row['naturalGas'];
            $prices['other'] = $row['other'];
            $building->setPrices($prices);
            break;
        }
    }
}


// Get the buildings' BASELINE prices
$sql = "SELECT * "
     . "FROM building_prices "
     . "WHERE submission_id=".$baselineID;

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

$prices = array("electricity"=> null, "fuel"=> null, "naturalGas"=> null, "other"=> null);
while($row = mysql_fetch_array($sql)){
    foreach ($buildingList as $building) {
        if($building->getId()== $row['building_id']){
            $prices['electricity'] = $row['electricity'];
            $prices['fuel'] = $row['fuel'];
            $prices['naturalGas'] = $row['naturalGas'];
            $prices['other'] = $row['other'];
            $building->setPricesBaseline($prices);
            break;
        }
    }
}



// Get the buildings' sources
$sql = "SELECT * "
     . "FROM building_sources "
     . "WHERE submission_id=".$sid;

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

while($row = mysql_fetch_array($sql)){
    foreach ($buildingList as $building) {
        if($building->getId()== $row['building_id']){
            $source = array("electricity" => 
                                array("heating" => $row['electricity_heating'], "cooling" => $row['electricity_cooling'], "other" => $row['electricity_other']),
                           "fuel" => 
                                array("heating" => $row['fuel_heating'], "cooling" => $row['fuel_cooling'], "other" => $row['fuel_other']),
                           "naturalGas" => 
                                array("heating" => $row['naturalGas_heating'], "cooling" => $row['naturalGas_cooling'], "other" => $row['naturalGas_other']),
                           "other" => 
                                array("heating" => $row['other_heating'], "cooling" => $row['other_cooling'], "other" => $row['other_other']));
            $building->setSource($source);
            break;
        }
    }
}

// Get the buildings' BASELINE sources
$sql = "SELECT * "
     . "FROM building_sources "
     . "WHERE submission_id=".$baselineID;

if(!$sql = mysql_query($sql, $database->connection)){
   //die('Ooops! We encountered a database error');
   die("Database Selection failed:".mysql_error());
}

while($row = mysql_fetch_array($sql)){
    foreach ($buildingList as $building) {
        if($building->getId()== $row['building_id']){
            $source = array("electricity" => 
                                array("heating" => $row['electricity_heating'], "cooling" => $row['electricity_cooling'], "other" => $row['electricity_other']),
                           "fuel" => 
                                array("heating" => $row['fuel_heating'], "cooling" => $row['fuel_cooling'], "other" => $row['fuel_other']),
                           "naturalGas" => 
                                array("heating" => $row['naturalGas_heating'], "cooling" => $row['naturalGas_cooling'], "other" => $row['naturalGas_other']),
                           "other" => 
                                array("heating" => $row['other_heating'], "cooling" => $row['other_cooling'], "other" => $row['other_other']));
            $building->setSourceBaseline($source);
            break;
        }
    }
}

// Get the buildings' actionplans
foreach ($buildingList as $building) {
    $bid = $building->getId();
    //print_r($building);
    $sql = "SELECT actionplan.*, building_actionplans.minmax "
     . "FROM actionplan "
     . "INNER JOIN building_actionplans "
     . "ON building_actionplans.actionplan_id = actionplan.id "
     . "WHERE building_actionplans.submission_id=".$sid." AND building_actionplans.building_id=".$bid;
    
    if(!$sql = mysql_query($sql, $database->connection)){
        //die('Ooops! We encountered a database error');
        die("Database Selection failed:".mysql_error());
    }
    
    $actions = array();
    while($row = mysql_fetch_array($sql)){
        if($row['minmax'] == "min"){
            $action = array("id"=>$row['id'], 
                            "type"=>$row['type'],
                            "minmax"=>$row['minmax'],
                            "heating"=> $row['heating_min'], 
                            "cooling"=>$row['cooling_min'], 
                            "other"=>$row['other_min'], 
                            "production"=>$row['res_min'], 
                            "cost"=>$row['cost_min']);
        }
        else{
            $action = array("id"=>$row['id'], 
                            "type"=>$row['type'],
                            "minmax"=>$row['minmax'],
                            "heating"=> $row['heating_max'], 
                            "cooling"=>$row['cooling_max'], 
                            "other"=>$row['other_max'], 
                            "production"=>$row['res_max'], 
                            "cost"=>$row['cost_max']);
        }
        
        array_push($actions, $action);
    }
    $building->setActions($actions);
    //print_r($actions);
}


echo $sid;
header('Location: doCalculations.php?compare='.$_GET["citySubmission"]);
