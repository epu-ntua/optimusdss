<?php

/* 
 * Giannis Tsapelas 2015
 */
require_once '../classes/Building.php';
require_once '../classes/CitySubmission.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$building = $_SESSION['newBuilding'];   


/*
** Set in the Building Object all the energy data,
** Consumption, Sources, Production
*/
$consumption = array("heating" => filter_input(INPUT_POST, 'consumptionHeating'), 
                     "cooling" => filter_input(INPUT_POST, 'consumptionCooling'), 
                     "other" => filter_input(INPUT_POST, 'consumptionOther'));

$building->setConsumptionBaseline($consumption);

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

$building->setSourceBaseline($source);
print_r($building->getSourceBaseline());

$prices = array("electricity" => filter_input(INPUT_POST, 'priceElectricity'), 
                "fuel" => filter_input(INPUT_POST, 'priceFuel'), 
                "naturalGas" => filter_input(INPUT_POST, 'priceNaturalGas'), 
                "other" => filter_input(INPUT_POST, 'priceOther'));

$building->setPricesBaseline($prices);

$building->setProductionBaseline(filter_input(INPUT_POST, 'production'));


/*
** Go to insertActions.php
*/
header('Location: insertActions.php');