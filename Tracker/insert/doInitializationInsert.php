<?php

/* 
 * Giannis Tsapelas 2015
 */
require_once '../classes/City.php';
require_once '../classes/CitySubmission.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/*
** We perform a new insert
*/ 
$_SESSION['mode'] = 'insert';

/*
** Initialize the Buildngs List and the Purpose List and the Sources List
*/  
$purposeList = array("heating", "cooling", "other");
$_SESSION['purposeList'] = $purposeList;
$sourcesList = array("electricity", "fuel", "naturalGas", "other");
$_SESSION['sourcesList'] = $sourcesList;

/*
** Create a new City and a new City Submission objects
*/ 
$_SESSION['City'] = new City();
$_SESSION['CitySubmission'] = new CitySubmission();


/*
** No new buildings are inserted
*/ 
$_SESSION['insertedBuildings'] = 0;

/*
** Go to insertGeneral.php
*/ 
header('Location: insertGeneral.php');