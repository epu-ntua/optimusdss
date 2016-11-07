<?php

/* 
 * Giannis Tsapelas 2015
 */

require_once '../classes/Building.php';
require_once '../classes/CitySubmission.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}



$buildingList = $_SESSION['CitySubmission']->getBuildings();
$newBuildingList = array();
foreach ($buildingList as $building) {
    $minmax = filter_input(INPUT_POST, 'minmax'.$building->getId());
    
    /*
    ** Empty the actions array
    */
    $actions = array();

    /*
    ** Get all consumption plans and add them in the actions array
    */
    $consumptionPlans = filter_input(INPUT_POST, 'consumptionPlans'.$building->getId(), FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if(!empty($consumptionPlans)){
        echo "Not Empty Consumption";
        echo '<br/>';

        for($i=0; $i < count($consumptionPlans); $i++){
            parse_str($consumptionPlans[$i], $helpArray);
            print_r($helpArray);
            echo '<br/>';

            if($minmax=="min"){
                $aConsumptionPlan = array("id"=>$helpArray['id'], 
                                          "type"=>"consumption",
                                          "minmax"=>"min",
                                          "heating"=> $helpArray['heating_min'], 
                                          "cooling"=>$helpArray['cooling_min'], 
                                          "other"=>$helpArray['other_min'], 
                                          "production"=>0, 
                                          "cost"=>0);
            }
            else{
                $aConsumptionPlan = array("id"=>$helpArray['id'], 
                                          "type"=>"consumption",
                                          "minmax"=>"max",
                                          "heating"=> $helpArray['heating_max'], 
                                          "cooling"=>$helpArray['cooling_max'], 
                                          "other"=>$helpArray['other_max'], 
                                          "production"=>0, 
                                          "cost"=>0);
            }

            print_r($aConsumptionPlan);
            echo '<br/>';

            array_push($actions, $aConsumptionPlan);
        }
    }
    
    /*
    ** Get all production plans and add them in the actions array
    */    
    $productionPlans = filter_input(INPUT_POST, 'productionPlans'.$building->getId(), FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if(!empty($productionPlans)){
        echo "Not Empty Production";
        echo '<br/>';

        for($i=0; $i < count($productionPlans); $i++){
            parse_str($productionPlans[$i], $helpArray);
            print_r($helpArray);
            echo '<br/>';

            if($minmax=="min"){
                $aProductionPlan = array("id"=>$helpArray['id'], 
                                         "type"=>"production",
                                         "minmax"=>"min",
                                         "heating"=> 0, 
                                         "cooling"=> 0, 
                                         "other"=> 0, 
                                         "production"=>$helpArray['production_min'], 
                                         "cost"=>0);
            }
            else{
                $aProductionPlan = array("id"=>$helpArray['id'], 
                                         "type"=>"production",
                                         "minmax"=>"max",
                                         "heating"=> 0, 
                                         "cooling"=> 0, 
                                         "other"=> 0, 
                                         "production"=>$helpArray['production_max'], 
                                         "cost"=>0);
            }

            print_r($aProductionPlan);
            echo '<br/>';

            array_push($actions, $aProductionPlan);
        }
    }

    /*
    ** Get all cost plans and add them in the actions array
    */  
    $costPlans = filter_input(INPUT_POST, 'costPlans'.$building->getId(), FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if(!empty($costPlans)){
        echo "Not Empty Cost";
        echo '<br/>';

        for($i=0; $i < count($costPlans); $i++){
            parse_str($costPlans[$i], $helpArray);
            print_r($helpArray);
            echo '<br/>';

            if($minmax=="min"){
                $aCostPlan = array("id"=>$helpArray['id'], 
                                   "type"=>"cost",
                                   "minmax"=>"min",
                                   "heating"=> 0, 
                                   "cooling"=> 0, 
                                   "other"=> 0, 
                                   "production"=>0, 
                                   "cost"=>$helpArray['cost_min']);
            }
            else{
                $aCostPlan = array("id"=>$helpArray['id'], 
                                   "type"=>"cost",
                                   "minmax"=>"max",
                                   "heating"=> 0, 
                                   "cooling"=> 0, 
                                   "other"=> 0, 
                                   "production"=>0, 
                                   "cost"=>$helpArray['cost_max']);
            }

            print_r($aCostPlan);
            echo '<br/>';

            array_push($actions, $aCostPlan);
        }
    }

    
    /*
    ** Add the actions to the Building Object
    */  
    $building->setActions($actions);
    array_push($newBuildingList, $building);
}    

$_SESSION['CitySubmission']->setBuildings($newBuildingList);

$_SESSION['CitySubmission']->setResultsProjectedCompare($_SESSION['CitySubmission']->getResultsProjected());
$_SESSION['CitySubmission']->setResultsPercentProjectedCompare($_SESSION['CitySubmission']->getResultsPercentProjected());

header('Location: doCalculations.php');