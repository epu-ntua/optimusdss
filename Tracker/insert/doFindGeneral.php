<?php

require_once '../classes/CitySubmission.php';
require_once '../classes/City.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once("../includes/config.php");
require_once("../includes/database.php");

//*** Get the Submission ID ***
$submissionID = 0;
$cityID = 0;
$baseline = null;
$city = strtoupper($_GET['city']);
$country = $_GET['country'];

// Get the most recent submission of this city from this user
$sql = "SELECT submission.id, submission.city_id, submission.year, submission.baseline "
     . "FROM submission "
     . "INNER JOIN city "
     . "ON submission.city_id = city.id "
     . "WHERE city.name = '" .$city. "'". " AND city.country = '" .$country. "'". " AND  user_id =".$_SESSION['user'];

if(!$sql = mysql_query($sql, $database->connection)){
    die('Ooops! We encountered a database error');
}
$_SESSION['City']->setCityName($city);    


$options = array();
while($row = mysql_fetch_array($sql)){
    $submissionID = $row['id'];
    $cityID = $row['city_id'];
    $baseline = $row['baseline'];
//    if(!in_array($row['year'], $options)){
//        array_push($options, $row['year']);
//    }
}

//$_SESSION['City']->setBaselineOptions($options);


//if($_SESSION['mode']=="insert"){
    $sql = "SELECT * FROM emissionfactors WHERE submission_id=".$submissionID;
    if(!$sql = mysql_query($sql, $database->connection)){
        die('Ooops! We encountered a database error');
    }

    $cntr = 0;
    while($row = mysql_fetch_array($sql)){
        $cntr++;
        $factors = array("electricity" => $row["electricity"], "fuel" => $row["fuel"], "naturalGas" => $row["naturalGas"], "other" => $row["other"]);
        $_SESSION['CitySubmission']->setFactors($factors);
    }
    if($cntr == 0){
        $sql = "SELECT * FROM emissionfactors WHERE submission_id=0";
        if(!$sql = mysql_query($sql, $database->connection)){
            die('Ooops! We encountered a database error');
        }

        while($row = mysql_fetch_array($sql)){
            $factors = array("electricity" => $row["electricity"], "fuel" => $row["fuel"], "naturalGas" => $row["naturalGas"], "other" => $row["other"]);
            $_SESSION['CitySubmission']->setFactors($factors);
        }
    }
//}

$sql = "SELECT * FROM targets WHERE city_id=".$cityID;
if(!$sql = mysql_query($sql, $database->connection)){
    die('Ooops! We encountered a database error');
}

$targets = array("consumption" => null, "emissions" => null, "cost" => null, "res" => null);
$_SESSION['CitySubmission']->setTargetYear(null);
while($row = mysql_fetch_array($sql)){
    $targets = array("consumption" => $row["consumption"], "emissions" => $row["emissions"], "cost" => $row["cost"], "res" => $row["res"]);
    $_SESSION['CitySubmission']->setTargetYear($row["year"]);
}
$_SESSION['City']->setTargets($targets);



$_SESSION['City']->setCountry($country);
$_SESSION['City']->setId($cityID);
$_SESSION['CitySubmission']->setBaseline($baseline);


$_SESSION['CityCompare'] = null;
$_SESSION['CitySubmissionCompare'] = null;

echo "hi";
if($_SESSION['mode']!="insert"){
    header('Location: insertGeneral.php');
    
}