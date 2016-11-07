<?php

/* 
 * Giannis Tsapelas 2015
 */
require_once '../classes/Building.php';
require_once '../classes/CitySubmission.php';
require_once '../classes/City.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/*
** Set in the Building Object all the building data,
** Name, Category
*/
$building = $_SESSION['newBuilding'];   
$building_name = filter_input(INPUT_POST, 'buildingName');
$building_name = str_replace(" ","_",$building_name);
$building->setName($building_name);
$building->setType(filter_input(INPUT_POST, 'buildingCategory'));

/*
** Set in the Building Object all the energy data,
** Consumption, Sources, Production
*/
$consumption = array("heating" => filter_input(INPUT_POST, 'consumptionHeating'), 
                     "cooling" => filter_input(INPUT_POST, 'consumptionCooling'), 
                     "other" => filter_input(INPUT_POST, 'consumptionOther'));

$building->setConsumption($consumption);

$source = array("electricity" => 
                    array("heating" => filter_input(INPUT_POST, 'electricityHeating')/100, 
                          "cooling" => filter_input(INPUT_POST, 'electricityCooling')/100, 
                          "other" => filter_input(INPUT_POST, 'electricityOther')/100),
                "fuel" => 
                    array("heating" => filter_input(INPUT_POST, 'fuelHeating')/100, 
                          "cooling" => filter_input(INPUT_POST, 'fuelCooling')/100, 
                          "other" => filter_input(INPUT_POST, 'fuelOther')/100),
                "naturalGas" => 
                    array("heating" => filter_input(INPUT_POST, 'naturalGasHeating')/100, 
                          "cooling" => filter_input(INPUT_POST, 'naturalGasCooling')/100, 
                          "other" => filter_input(INPUT_POST, 'naturalGasOther')/100),
                "other" => 
                    array("heating" => filter_input(INPUT_POST, 'otherHeating')/100, 
                          "cooling" => filter_input(INPUT_POST, 'otherCooling')/100, 
                          "other" => filter_input(INPUT_POST, 'otherOther')/100) );

$building->setSource($source);
print_r($source);


$prices = array("electricity" => filter_input(INPUT_POST, 'priceElectricity'), 
                "fuel" => filter_input(INPUT_POST, 'priceFuel'), 
                "naturalGas" => filter_input(INPUT_POST, 'priceNaturalGas'), 
                "other" => filter_input(INPUT_POST, 'priceOther'));

$building->setPrices($prices);
print_r($building->getPrices());

$building->setProduction(filter_input(INPUT_POST, 'production'));

echo "<BR/>".$_SESSION['CitySubmission']->getYear();
echo "<BR/>".$_SESSION['CitySubmission']->getBaseline();
if($_SESSION['CitySubmission']->getYear() != $_SESSION['CitySubmission']->getBaseline()){
	echo "Different Baseline Year";

    /*
    ** Get Baseline Data if any
    */
    require_once("../includes/config.php");
    require_once("../includes/database.php");

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

    if($baselineID != null){
		echo "Baseline NOT NULL";
        $sql = "SELECT * "
             . "FROM building_consumption "
             . "WHERE submission_id = '" . $baselineID. "' AND building_id = '" . $building->getId(). "'";
        if(!$sql = mysql_query($sql, $database->connection)){
            die("Database connection failed:".mysql_error()); 
            //die('Ooops! We encountered a database error');
        }  
        while($row = mysql_fetch_array($sql)){
            $consumption = array("heating" => $row['heating'], "cooling" => $row['cooling'], "other" => $row['other']);
            $building->setConsumptionBaseline($consumption);
        }
        print_r($consumption);
        
        $sql = "SELECT * "
             . "FROM building_prices "
             . "WHERE submission_id = '" . $baselineID. "' AND building_id = '" . $building->getId(). "'";
        if(!$sql = mysql_query($sql, $database->connection)){
            die("Database connection failed:".mysql_error()); 
            //die('Ooops! We encountered a database error');
        }  
        while($row = mysql_fetch_array($sql)){
            $prices = array("electricity" => $row['electricity'], "naturalGas" => $row['naturalGas'], "fuel" => $row['fuel'], "other" => $row['other']);
            $building->setPricesBaseline($prices);
        }
        print_r($prices);

        $sql = "SELECT * "
             . "FROM building_production "
             . "WHERE submission_id = '" . $baselineID. "' AND building_id = '" . $building->getId(). "'";
        if(!$sql = mysql_query($sql, $database->connection)){
            die("Database connection failed:".mysql_error()); 
            //die('Ooops! We encountered a database error');
        }  
        while($row = mysql_fetch_array($sql)){
            $building->setProductionBaseline($row['production']);
        }

        $sql = "SELECT * "
             . "FROM building_sources "
             . "WHERE submission_id = '" . $baselineID. "' AND building_id = '" . $building->getId(). "'";
        if(!$sql = mysql_query($sql, $database->connection)){
            die("Database connection failed:".mysql_error()); 
            //die('Ooops! We encountered a database error');
        }  
        while($row = mysql_fetch_array($sql)){
            $source = array("electricity" => 
                                array("heating" => $row['electricity_heating'], 
                                      "cooling" => $row['electricity_cooling'], 
                                      "other" =>   $row['electricity_other']),
                            "fuel" => 
                                array("heating" => $row['fuel_heating'], 
                                      "cooling" => $row['fuel_cooling'], 
                                      "other" => $row['fuel_other']),
                            "naturalGas" => 
                                array("heating" => $row['naturalGas_heating'], 
                                      "cooling" => $row['naturalGas_cooling'], 
                                      "other" => $row['naturalGas_other']),
                            "other" => 
                                array("heating" => $row['other_heating'], 
                                      "cooling" => $row['other_cooling'], 
                                      "other" => $row['other_other']) );

            $building->setSourceBaseline($source);
        }
    }
}
else{
    $building->setConsumptionBaseline($building->getConsumption());
    $building->setPricesBaseline($building->getPrices());
    $building->setProductionBaseline($building->getProduction());
    $building->setSourceBaseline($building->getSource());
    print_r($building->getSourceBaseline());
}


/*
** Go to insertActions.php
*/
header('Location: insertBaselineData.php');