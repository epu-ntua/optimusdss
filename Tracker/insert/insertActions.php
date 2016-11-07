<!DOCTYPE html>
<!--
Giannis Tsapelas 2015
-->
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        
        
        <title></title>
        <link rel="stylesheet" type="text/css" href="../css/general.css">
        <link rel="stylesheet" type="text/css" href="../css/buttons.css">
        <link rel="stylesheet" type="text/css" href="../css/insert.css">
        <link rel="stylesheet" type="text/css" href="../css/insertActions.css">
        
        <script src="../scripts/navigationScript.js"> </script>
        <script src="../scripts/actionScript.js"> </script>
    </head>
    <?php
        require_once '../classes/CitySubmission.php';
        require_once '../classes/Building.php';
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $building = $_SESSION['newBuilding']; 
        $actions = $building->getActions();
        $actionsIDs = array();
        for($i=0; $i<count($actions); $i++){
            array_push($actionsIDs, $actions[$i]['id']);
        }
        
        // Get the available actions
        require_once("../includes/config.php");
        require_once("../includes/database.php");
        
        $sql_consumption = "SELECT * FROM actionplan WHERE type='consumption' AND active = 1";
        if(!$sql_consumption = mysql_query($sql_consumption, $database->connection)){
            die('Ooops! We encountered a database error');
        }
        $sql_production = "SELECT * FROM actionplan WHERE type='production' AND active = 1";
        if(!$sql_production = mysql_query($sql_production, $database->connection)){
            die('Ooops! We encountered a database error');
        }
        $sql_cost = "SELECT * FROM actionplan WHERE type='cost' AND active = 1";
        if(!$sql_cost = mysql_query($sql_cost, $database->connection)){
            die('Ooops! We encountered a database error');
        }
    ?>
    <body>
     <div class="container" id="workspace">
       <div class="container" id="header">
           <img src="../images/sceaf_banner.jpg" id="banner" alt="Optimus Banner">
           <?php require_once '../navBar.php'; ?>
       </div>
       <div class="container" id="content">
<!--**********************************************-->
<!--****************Guide Insert******************-->
<!--**********************************************-->

           <?php require_once 'guideInsert.php'; ?>

<!--**********************************************-->
<!--***************Guide Building*****************-->
<!--**********************************************-->

           <?php require_once 'guideBuilding.php'; ?>

<!--**********************************************-->
<!--***********Building Actions Form**************-->
<!--**********************************************-->
           <h2 style='color: #054993; font-size: 22pt; '>Select the building's <span style='text-decoration: underline;'>new</span> action plans to be applied</h2>
           <form role="form" class="form-horizontal insertForm" action='doInsertActions.php'  method='post'>
                <?php require 'actionTables.php'; ?>
                
                <div>
                    <a href="insertBaselineData.php?bid=<?php echo $building->getId() ?>"  class="btn btn-default" role="button" id="backBtn">Back</a>
                    <button type="submit" class="btn btn-default" id="nextBtn">Submit Building</button>
                    <a href="insertBuildings.php" class="btn btn-default" role="button" id="cancelBtn">Cancel</a>
                </div>
           </form>
       </div>
       <div class="container" id="footer">
           <p>Copyright © 2016 EPU NTUA</p>
       </div>
     </div>
    </body>
</html>
