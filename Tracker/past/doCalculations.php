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


// If submission year is not the same as baseline year, then find the baseline prices and factors
if($_SESSION[$_GET['compare']]->getYear() != $_SESSION[$_GET['compare']]->getBaseline()){

    /*
    ** Get Baseline ID
    */
    require_once("../includes/config.php");
    require_once("../includes/database.php");

    $sql = "SELECT id "
         . "FROM submission "
         . "WHERE city_id = '" . $_SESSION['City']->getId(). "' AND user_id = '" . $_SESSION['user'] . "' AND year = '" . $_SESSION[$_GET['compare']]->getBaseline() . "'";

    if(!$sql = mysql_query($sql, $database->connection)){
        die("Database connection failed:".mysql_error()); 
        //die('Ooops! We encountered a database error');
    }

    $baselineID = null;
    while($row = mysql_fetch_array($sql)){
        $baselineID = $row['id'];
    }
    
    // Baseline ID must always be found
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
            $_SESSION[$_GET['compare']]->setFactorsBaseline($factorsBaseline);
        }
    }
}
else{
    // Else use the current prices and factors
    $_SESSION[$_GET['compare']]->setFactorsBaseline($_SESSION[$_GET['compare']]->getFactors());
    
}



// Initialozation of initital values and reduction
$categoryList = array("city", "administration", "hospitals", "education", "entertainment", "sport", "other");
$resultsList = array("consumption", "emissions", "cost", "res");
foreach($categoryList as $category){
    foreach($resultsList as $results){
        $initial[$category][$results] = 0;
        $reduction[$category][$results] = 0;
        $baseline[$category][$results] = 0;
    }
}

$buildingList = $_SESSION[$_GET['compare']]->getBuildings();
//print_r($buildingList);

// For each building calculate the reduction to the category
foreach($buildingList as $building){
    if($building->getStatus() == "included"){
        if($_SESSION[$_GET['compare']]->getYear() != $_SESSION[$_GET['compare']]->getBaseline()){
            $sql = "SELECT * "
                 . "FROM building_consumption "
                 . "WHERE submission_id = '" . $baselineID. "' AND building_id=".$building->getId();
            if(!$sql = mysql_query($sql, $database->connection)){
                die("Database connection failed:".mysql_error()); 
                //die('Ooops! We encountered a database error');
            }  
            while($row = mysql_fetch_array($sql)){
                $consumption = array("heating" => $row['heating'], "cooling" => $row['cooling'], "other" => $row['other']);
                $building->setConsumptionBaseline($consumption);
            }
            
            $sql = "SELECT * "
                 . "FROM building_sources "
                 . "WHERE submission_id = '" . $baselineID. "' AND building_id=".$building->getId();
            if(!$sql = mysql_query($sql, $database->connection)){
                die("Database connection failed:".mysql_error()); 
                //die('Ooops! We encountered a database error');
            }  
            while($row = mysql_fetch_array($sql)){
                $source = array("electricity" => 
                                array("heating" => $row['electricity_heating'], "cooling" => $row['electricity_cooling'], "other" => $row['electricity_other']),
                           "fuel" => 
                                array("heating" => $row['fuel_heating'], "cooling" => $row['fuel_cooling'], "other" => $row['fuel_other']),
                           "naturalGas" => 
                                array("heating" => $row['naturalGas_heating'], "cooling" => $row['naturalGas_cooling'], "other" => $row['naturalGas_other']),
                           "other" => 
                                array("heating" => $row['other_heating'], "cooling" => $row['other_cooling'], "other" => $row['other_other']) );
                $building->setSourceBaseline($source);
            }
            
            $sql = "SELECT * "
                 . "FROM building_production "
                 . "WHERE submission_id = '" . $baselineID. "' AND building_id=".$building->getId();
            if(!$sql = mysql_query($sql, $database->connection)){
                die("Database connection failed:".mysql_error()); 
                //die('Ooops! We encountered a database error');
            }  
            while($row = mysql_fetch_array($sql)){
                $production = $row['production'];
                $building->setProductionBaseline($production);
            }
            
        }
        else{
            $building->setConsumptionBaseline($building->getConsumption());
            $building->setSourceBaseline($building->getSource());
            $building->setProductionBaseline($building->getProduction());
        }
        
        
        
        
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
        echo "Actions: "; echo " </br> ";    
        //print_r($actions); echo " </br> "; echo " </br> "; 
        
        
        // If submission year is not the same as baseline year, then find the baseline prices
        if($_SESSION[$_GET['compare']]->getYear() != $_SESSION[$_GET['compare']]->getBaseline()){
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
        

        // Calculate the consumption reduction of the building for each purpose (heating, cooling, other)
        // and add it to the right category
        $purposeList = array("heating", "cooling", "other");
        foreach($purposeList as $purpose){
            //$consumptionReduction[$purpose] = $building->getConsumption()[$purpose] * $actions[$purpose];
            $consumptionReduction[$purpose] = $building->getConsumptionBaseline()[$purpose] * $actions[$purpose];
            $initial[$building->getType()]['consumption'] += $building->getConsumption()[$purpose];
            $reduction[$building->getType()]['consumption'] += $consumptionReduction[$purpose];
            $baseline[$building->getType()]['consumption'] += $building->getConsumptionBaseline()[$purpose];
        }
        echo "Consumption Initial: "; echo " </br> ";    
        //print_r($initial[$building->getType()]['consumption']); echo " </br> "; echo " </br> "; 
        
        echo "Consumption Baseline: "; echo " </br> ";    
        //print_r($baseline[$building->getType()]['consumption']); echo " </br> "; echo " </br> "; 
        
        echo "Consumption Reduction: "; echo " </br> ";    
        //print_r($consumptionReduction); echo " </br> "; 
        //print_r($reduction[$building->getType()]['consumption']); echo " </br> "; echo " </br> "; 
        

        $sourcesList = array("electricity", "naturalGas", "fuel", "other");
        foreach ($sourcesList as $source) {
            foreach($purposeList as $purpose){
                $initial[$building->getType()]['emissions'] += $building->getConsumption()[$purpose] * $building->getSource()[$source][$purpose] * $_SESSION[$_GET['compare']]->getFactors()[$source] / 1000;
                $baseline[$building->getType()]['emissions'] += $building->getConsumptionBaseline()[$purpose] * $building->getSourceBaseline()[$source][$purpose] * $_SESSION[$_GET['compare']]->getFactors()[$source] / 1000;     
            }
        }
        foreach ($sourcesList as $source) {
            foreach($purposeList as $purpose){
                $reduction[$building->getType()]['emissions'] += $building->getConsumptionBaseline()[$purpose] * $building->getSource()[$source][$purpose] * $_SESSION[$_GET['compare']]->getFactors()[$source] * $actions[$purpose] / 1000;     
            }
        }
        echo "Emissions Initial: "; echo " </br> ";    
        //print_r($initial[$building->getType()]['emissions']); echo " </br> "; echo " </br> "; 
        
        echo "Emissions Baseline: "; echo " </br> ";    
        //print_r($baseline[$building->getType()]['emissions']); echo " </br> "; echo " </br> "; 
        
        echo "Emissions Reduction: "; echo " </br> ";    
        //print_r($reduction[$building->getType()]['emissions']); echo " </br> "; echo " </br> "; 

        

        foreach ($sourcesList as $source) {
            foreach($purposeList as $purpose){
                $initial[$building->getType()]['cost'] += $building->getConsumption()[$purpose] * $building->getSource()[$source][$purpose] * $building->getPrices()[$source];     
                $baseline[$building->getType()]['cost'] += $building->getConsumptionBaseline()[$purpose] * $building->getSourceBaseline()[$source][$purpose] * $building->getPrices()[$source];
            }
        }
        foreach ($sourcesList as $source) {
            foreach($purposeList as $purpose){
                $reduction[$building->getType()]['cost'] += $building->getConsumptionBaseline()[$purpose] * $building->getSource()[$source][$purpose] * $building->getPrices()[$source] * $actions[$purpose];
                $reduction[$building->getType()]['cost'] += $building->getConsumptionBaseline()[$purpose] * $building->getSource()[$source][$purpose] * $building->getPrices()[$source] * ( 1 - $actions[$purpose]) * $actions['cost'];
            }
        }

        $initial[$building->getType()]['res'] = $building->getProduction();
        $baseline[$building->getType()]['res'] = $building->getProductionBaseline();
        $reduction[$building->getType()]['res'] = $building->getProductionBaseline() * $actions['res'];
        
        echo "RES Initial: "; echo " </br> ";    
        //print_r($initial[$building->getType()]['res']); echo " </br> "; echo " </br> "; 
        
        echo "RES Baseline: "; echo " </br> ";    
        //print_r($baseline[$building->getType()]['res']); echo " </br> "; echo " </br> "; 
        
        echo "RES Increase: "; echo " </br> ";    
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


$_SESSION[$_GET['compare']]->setResultsPercentCurrent($resultsPercentCurrent);
$_SESSION[$_GET['compare']]->setResultsPercentProjected($resultsPercentProjected);
$_SESSION[$_GET['compare']]->setResultsCurrent($resultsCurrent);
$_SESSION[$_GET['compare']]->setResultsProjected($resultsProjected);


//print_r($_SESSION[$_GET['compare']]->getResultsPercentProjected());

header('Location: submissionOverview.php?view=city');
