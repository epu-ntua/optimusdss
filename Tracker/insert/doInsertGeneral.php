<?php

/* 
 * Giannis Tsapelas 2015
 */

require_once '../classes/City.php';
require_once '../classes/CitySubmission.php';
require_once '../classes/Building.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


/*
** Set in the City Object all the general data,
** Name, Country, Targets, Baseline Year
*/
if($_SESSION['mode'] == "insert"){
    $_SESSION['City']->setCityName(strtoupper(filter_input(INPUT_POST, 'cityName')));
    $_SESSION['City']->setCountry(filter_input(INPUT_POST, 'country'));
}

$targets = array("consumption" => filter_input(INPUT_POST, 'targetConsumption') / 100, 
                 "emissions" => filter_input(INPUT_POST, 'targetEmissions') / 100, 
                 "cost" => filter_input(INPUT_POST, 'targetCost') / 100, 
                 "res" => filter_input(INPUT_POST, 'targetRes') / 100);
        
$_SESSION['City']->setTargets($targets);



/*
** Set in the CitySubmission Object all the general data,
** Year, Visibility, Emission Factors
*/
if($_SESSION['mode'] == "insert"){
    $_SESSION['CitySubmission']->setYear(filter_input(INPUT_POST, 'year'));
}
$_SESSION['CitySubmission']->setTargetYear(filter_input(INPUT_POST, 'targetYear'));
$_SESSION['CitySubmission']->setVisibility(filter_input(INPUT_POST, 'visibility'));
$_SESSION['CitySubmission']->setBaseline(filter_input(INPUT_POST, 'baseline'));
$_SESSION['CitySubmission']->setName(filter_input(INPUT_POST, 'name'));




$factors = array("electricity" => filter_input(INPUT_POST, 'factorElectricity'), 
                 "fuel" => filter_input(INPUT_POST, 'factorFuel'), 
                 "naturalGas" => filter_input(INPUT_POST, 'factorNaturalGas'), 
                 "other" => filter_input(INPUT_POST, 'factorOther'));

$_SESSION['CitySubmission']->setFactors($factors);


/*
** Find all the buildings associated with the given city
** and add them to the CitySubmission.
** If a buildings already exists in the list (its id),
** then do not add it
*/
require_once("../includes/config.php");
require_once("../includes/database.php");

$sql = "SELECT building.* "
     . "FROM building "
     . "INNER JOIN city "
     . "ON building.city_id = city.id "
     . "WHERE city.name = '" . $_SESSION['City']->getCityName(). "' AND city.country = '" . $_SESSION['City']->getCountry(). "'";
echo $sql;
if(!$sql = mysql_query($sql, $database->connection)){
    die("Database connection failed:".mysql_error()); 
    //die('Ooops! We encountered a database error');
}

$buildings = $_SESSION['CitySubmission']->getBuildings();
$buildingIDs = array();
for($i=0; $i<count($buildings); $i++){
    array_push($buildingIDs, $buildings[$i]->getId());
}
print_r ($buildingIDs);
while($row = mysql_fetch_array($sql)){
    $building = new Building();
    $building->setId($row['id']);
    $building->setName($row['name']);
    $building->setType($row['type']);
    echo $row['id'];
    if(!in_array($row['id'], $buildingIDs)){
        if($_SESSION['mode'] != "insert"){
            $building->setStatus("excluded_nodata");
        }
        $_SESSION['CitySubmission']->addBuilding($building);
        echo "added";
    }
}



/*
** Now go to insertBuildings.php
*/
header('Location: insertBuildings.php');