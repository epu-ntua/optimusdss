<?php

/* 
 * Giannis Tsapelas 2015
 */

require_once '../classes/Building.php';
require_once '../classes/CitySubmission.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/*
** Get all the Buildings and exclude the building with the specified id
*/
$bid = filter_input(INPUT_GET, 'bid');
$buildings = $_SESSION['CitySubmission']->getBuildings();
for($i=0; $i<count($buildings); $i++){
    if($buildings[$i]->getId() == $bid){
        if($buildings[$i]->getStatus()=='excluded_data'){
            $buildings[$i]->setStatus("included");
        }
        else if($buildings[$i]->getStatus()=='excluded_nodata'){
            $buildings[$i]->setStatus("nodata");
        }
        break;
    }
}


/*
** Now go to insertBuildings.php
*/
header('Location: insertBuildings.php');