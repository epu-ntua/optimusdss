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
        <link rel="stylesheet" type="text/css" href="../css/insertBuildings.css">
        
        <script src="../scripts/navigationScript.js"> </script>
        <script src="../scripts/buildingsScript.js"> </script>
    </head>
    <?php
        require_once '../classes/Building.php';
        require_once '../classes/CitySubmission.php';
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
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
<!--***************Buildings Table****************-->
<!--**********************************************-->
           
            <h1>Inserted Buildings</h1>
            
            <a href="insertEnergyData.php?" class="btn btn-success btn-md" role="button" id="newBuildingBtn">+ New Building</a>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th hidden>ID</th>
                        <th>Status</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
              <?php
                $buildingList = $_SESSION['CitySubmission']->getBuildings();
                for($i=0; $i<count($buildingList); $i++){
                    $building = $buildingList[$i];
              ?>      
                    <tr>
                        <td class="filterable-cell" hidden><?php echo $building->getID() ?></td>
                        <td class="filterable-cell"><?php echo '<span class="label '; 
                                                          if($building->getStatus()=='included'){
                                                              echo 'label-success"> included';
                                                          }
                                                          else if(($building->getStatus()=='excluded_data')||($building->getStatus()=='excluded_nodata')){
                                                              echo 'label-danger"> excluded';
                                                          }
                                                          else{
                                                              echo 'label-default"> no data';
                                                          }
                                                          echo '</span>'; 
                                                    ?></td>
                        <td class="filterable-cell"><?php echo $building->getName() ?></td>
                        <td class="filterable-cell"><?php echo ucfirst($building->getType()) ?></td>
                        <td class="filterable-cell"><a href="insertEnergyData.php?bid=<?php echo $building->getId() ?>" class="btn btn-primary btn-sm" role="button">Insert Data</a></td>                       
                        <td class="filterable-cell"><?php if(($building->getStatus()=='included')||($building->getStatus()=='nodata')){ 
                                                            echo '<a href="doBuildingExcluded.php?bid='.$building->getId().'" class="btn btn-danger btn-sm" role="button">Exclude</a>';
                                                          }
                                                          else if(($building->getStatus()=='excluded_data')||($building->getStatus()=='excluded_nodata')){
                                                              echo '<a href="doBuildingIncluded.php?bid='.$building->getId().'" class="btn btn-success btn-sm" role="button">Include</a>';
                                                          }
                                                    ?> 
                        </td>
                    </tr>
              <?php
                }
              ?>
                </tbody>
            </table>
            
            <div>
                <a href="insertGeneral.php" class="btn btn-default" role="button" id="backBtn">Back</a>
                <a href="doCalculations.php" class="btn btn-default" role="button" id="nextBtn">Next</a>
                <a href="../intro.php" class="btn btn-default" role="button" id="cancelBtn">Cancel Submission</a>
            </div>
       </div>
       <div class="container" id="footer">
           <p>Copyright © 2016 EPU NTUA</p>
       </div>
     </div>
    </body>
</html>
