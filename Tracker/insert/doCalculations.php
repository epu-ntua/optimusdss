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

// If submission year is not the same as baseline year, then find the baseline factors
if($_SESSION['CitySubmission']->getYear() != $_SESSION['CitySubmission']->getBaseline()){

    /*
    ** Get Baseline ID
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
        //Get baseline factors
        $sql = "SELECT * "
             . "FROM emissionfactors "
             . "WHERE submission_id = '" . $baselineID. "'";
        if(!$sql = mysql_query($sql, $database->connection)){
            die("Database connection failed:".mysql_error()); 
            //die('Ooops! We encountered a database error');
        }  
        while($row = mysql_fetch_array($sql)){
            $factorsBaseline = array("electricity" => $row['electricity'], "naturalGas" => $row['naturalGas'], "fuel" => $row['fuel'], "other" => $row['other']);
            $_SESSION['CitySubmission']->setFactorsBaseline($factorsBaseline);
        }
    }
	else{
		$_SESSION['CitySubmission']->setFactorsBaseline($_SESSION['CitySubmission']->getFactors());
	}
}
else{
    // Else use the current factors
    $_SESSION['CitySubmission']->setFactorsBaseline($_SESSION['CitySubmission']->getFactors());
}





// Set the initital values and reduction to zero
// Initial is the data for the current year!
// Reduction is the projected reduction in consumption, emmisions, cost and (increase) of res, for the NEXT year
$categoryList = array("city", "administration", "hospitals", "education", "entertainment", "sport", "other");
$resultsList = array("consumption", "emissions", "cost", "res");
foreach($categoryList as $category){
    foreach($resultsList as $results){
        $initial[$category][$results] = 0;
        $reduction[$category][$results] = 0;
        $baseline[$category][$results] = 0;
    }
}

$buildingList = $_SESSION['CitySubmission']->getBuildings();

// For each building calculate the reduction to the category
foreach($buildingList as $building){
    //echo $building->getId();
    if($building->getStatus() == "included"){
        //For each building calculate the total percentage of the selected actionplans
        $actions['heating'] = 1;
        $actions['cooling'] = 1;
        $actions['other'] = 1;
        $actions['cost'] = 1;
        $actions['res'] = 1;

        foreach ($building->getActions() as $action){
            $actions['heating'] *= 1 - ($action['heating'] / 100);
            $actions['cooling'] *= 1 - ($action['cooling'] / 100);
            $actions['other'] *= 1 - ($action['other'] / 100);
            $actions['cost'] *= 1 - ($action['cost'] / 100);
            $actions['res'] *= 1 - ($action['production'] / 100);
        }
        $actions['heating'] = 1 - $actions['heating'];
        $actions['cooling'] = 1 - $actions['cooling'];
        $actions['other'] = 1 - $actions['other'];
        $actions['cost'] = 1 - $actions['cost'];
        $actions['res'] = 1 - $actions['res'];
        //echo "Actions: "; echo " </br> ";    
        //print_r($actions); echo " </br> "; echo " </br> ";    
        
        
        // If submission year is not the same as baseline year, then find the baseline prices
        if($_SESSION['CitySubmission']->getYear() != $_SESSION['CitySubmission']->getBaseline()){
            //Get baseline prices
            $sql = "SELECT * "
                 . "FROM building_prices "
                 . "WHERE submission_id = '" . $baselineID. "'"." AND building_id=".$building->getId();
            if(!$sql = mysql_query($sql, $database->connection)){
                die("Database connection failed:".mysql_error()); 
                //die('Ooops! We encountered a database error');
            }  
            while($row = mysql_fetch_array($sql)){
                $pricesBaseline = array("electricity" => $row['electricity'], "naturalGas" => $row['naturalGas'], "fuel" => $row['fuel'], "other" => $row['other']);
                $building->setPricesBaseline($pricesBaseline);
            }
        }
        else{
            $building->setPricesBaseline($building->getPrices());
        }
        //print_r($building->getPrices()); echo " </br> "; echo " </br> ";    
        
        // Calculate the consumption reduction of the building for each purpose (heating, cooling, other)
        // and add it to the right category
        $purposeList = array("heating", "cooling", "other");
        foreach($purposeList as $purpose){
            //reduction of purpose = consumption of purpose x actionplans_% of purpose
            //$consumptionReduction[$purpose] = $building->getConsumption()[$purpose] * $actions[$purpose];
            $consumptionReduction[$purpose] = $building->getConsumptionBaseline()[$purpose] * $actions[$purpose];
            //total consumption = sum of consumptions for all purposes
            $initial[$building->getType()]['consumption'] += $building->getConsumption()[$purpose];
            //total reduction = sum of reductions for all purposes
            $reduction[$building->getType()]['consumption'] += $consumptionReduction[$purpose];
            //total baseline consumtpion = sum of baseline consumptions for all purposes
            $baseline[$building->getType()]['consumption'] += $building->getConsumptionBaseline()[$purpose];
        }
        //echo "Consumption Initial: "; echo " </br> ";    
        //print_r($initial[$building->getType()]['consumption']); echo " </br> "; echo " </br> "; 
        
        //echo "Consumption Baseline: "; echo " </br> ";    
        //print_r($baseline[$building->getType()]['consumption']); echo " </br> "; echo " </br> "; 
        
        //echo "Consumption Reduction: "; echo " </br> ";    
        //print_r($consumptionReduction); echo " </br> "; 
        //print_r($reduction[$building->getType()]['consumption']); echo " </br> "; echo " </br> "; 

        
        // Calculate the emissions reduction of the building for each purpose (heating, cooling, other)
        // and add it to the right category
        $sourcesList = array("electricity", "naturalGas", "fuel", "other");
        foreach ($sourcesList as $source) {
            foreach($purposeList as $purpose){
                $initial[$building->getType()]['emissions'] += $building->getConsumption()[$purpose] * $building->getSource()[$source][$purpose] * $_SESSION['CitySubmission']->getFactors()[$source];
                $baseline[$building->getType()]['emissions'] += $building->getConsumptionBaseline()[$purpose] * $building->getSourceBaseline()[$source][$purpose] * $_SESSION['CitySubmission']->getFactorsBaseline()[$source];     
            }
        }
        foreach ($sourcesList as $source) {
            foreach($purposeList as $purpose){
                $reduction[$building->getType()]['emissions'] += $building->getConsumptionBaseline()[$purpose] * $building->getSource()[$source][$purpose] * $_SESSION['CitySubmission']->getFactors()[$source] * $actions[$purpose];     
            }
        }
        //echo "Emissions Initial: "; echo " </br> ";    
        //print_r($initial[$building->getType()]['emissions']); echo " </br> "; echo " </br> "; 
        
        //echo "Emissions Baseline: "; echo " </br> ";    
        //print_r($baseline[$building->getType()]['emissions']); echo " </br> "; echo " </br> "; 
        
        //echo "Emissions Reduction: "; echo " </br> ";    
        //print_r($reduction[$building->getType()]['emissions']); echo " </br> "; echo " </br> "; 

        
        
        // Calculate the cost reduction of the building for each purpose (heating, cooling, other)
        // and add it to the right category
        foreach ($sourcesList as $source) {
            foreach($purposeList as $purpose){
                $initial[$building->getType()]['cost'] += $building->getConsumption()[$purpose] * $building->getSource()[$source][$purpose] * $building->getPrices()[$source];     
                $baseline[$building->getType()]['cost'] += $building->getConsumptionBaseline()[$purpose] * $building->getSourceBaseline()[$source][$purpose] * $building->getPricesBaseline()[$source];
            }
        }
        foreach ($sourcesList as $source) {
            foreach($purposeList as $purpose){
                $reduction[$building->getType()]['cost'] += $building->getConsumptionBaseline()[$purpose] * $building->getSource()[$source][$purpose] * $building->getPrices()[$source] * $actions[$purpose];
                $reduction[$building->getType()]['cost'] += $building->getConsumptionBaseline()[$purpose] * $building->getSource()[$source][$purpose] * $building->getPrices()[$source] * ( 1 - $actions[$purpose]) * $actions['cost'];
            }
        }
        //echo "Cost Initial: "; echo " </br> ";    
        //print_r($initial[$building->getType()]['cost']); echo " </br> "; echo " </br> "; 
        
        //echo "Cost Baseline: "; echo " </br> ";    
        //print_r($baseline[$building->getType()]['cost']); echo " </br> "; echo " </br> "; 
        
        //echo "Cost Reduction: "; echo " </br> ";    
        //print_r($reduction[$building->getType()]['cost']); echo " </br> "; echo " </br> "; 
        
        
        // Calculate the res increase of the building 
        // and add it to the right category
        $initial[$building->getType()]['res'] = $building->getProduction();
        $baseline[$building->getType()]['res'] = $building->getProductionBaseline();
        $reduction[$building->getType()]['res'] = $building->getProductionBaseline() * $actions['res'];
        
        //echo "RES Initial: "; echo " </br> ";    
        //print_r($initial[$building->getType()]['res']); echo " </br> "; echo " </br> "; 
        
        //echo "RES Baseline: "; echo " </br> ";    
        //print_r($baseline[$building->getType()]['res']); echo " </br> "; echo " </br> "; 
        
        //echo "RES Increase: "; echo " </br> ";    
        //print_r($reduction[$building->getType()]['res']); echo " </br> "; echo " </br> "; 
        
    }
}


// Sum the results to City Level
$categoryList = array("city", "administration", "hospitals", "education", "entertainment", "sport", "other");
$resultsList = array("consumption", "emissions", "cost", "res");
foreach($categoryList as $category){
    foreach($resultsList as $results){
            $initial['city'][$results] += $initial[$category][$results];
            $baseline['city'][$results] += $baseline[$category][$results];
            $reduction['city'][$results] += $reduction[$category][$results];
    }
}



//resultsCurrent -> Se kWh h metavolh metaksu baseline kai current year = baseline - initial
//resultsProjected -> Se kWh h metavolh metaksu baseline kai next year = baseline - (initial - reduction)
$categoryList = array("city", "administration", "hospitals", "education", "entertainment", "sport", "other");
$resultsList = array("consumption", "emissions", "cost", "res");
foreach($categoryList as $category){
    foreach($resultsList as $results){
        if($baseline[$category][$results] != 0){
            if($results != "res"){
                $resultsCurrent[$category][$results] = round($baseline[$category][$results] - $initial[$category][$results], 1);
                $resultsProjected[$category][$results] = round($baseline[$category][$results] - ($initial[$category][$results] - $reduction[$category][$results]), 1);

                $resultsPercentCurrent[$category][$results] = round(($baseline[$category][$results] - $initial[$category][$results]) / $baseline[$category][$results], 3);
                $resultsPercentProjected[$category][$results] = round(($baseline[$category][$results] - ($initial[$category][$results] - $reduction[$category][$results])) / $baseline[$category][$results], 3);
            }    
            else{
                $resultsCurrent[$category][$results] = round($initial[$category][$results] - $baseline[$category][$results], 1);
                $resultsProjected[$category][$results] = round($initial[$category][$results] + $reduction[$category][$results] - $baseline[$category][$results], 1);

                $resultsPercentCurrent[$category][$results] = round(($initial[$category][$results] - $baseline[$category][$results]) / $baseline[$category][$results], 3);
                $resultsPercentProjected[$category][$results] = round(($initial[$category][$results] + $reduction[$category][$results] - $baseline[$category][$results]) / $baseline[$category][$results], 3);
            }
        }   
        else{
            $resultsCurrent[$category][$results] = 0;
            $resultsProjected[$category][$results] = 0;
            $resultsPercentCurrent[$category][$results] = 0;
            $resultsPercentProjected[$category][$results] = 0;
        }
    }
}


$_SESSION['CitySubmission']->setResultsPercentCurrent($resultsPercentCurrent);
$_SESSION['CitySubmission']->setResultsPercentProjected($resultsPercentProjected);
$_SESSION['CitySubmission']->setResultsCurrent($resultsCurrent);
$_SESSION['CitySubmission']->setResultsProjected($resultsProjected);


header('Location: insertOverview.php?view=city');