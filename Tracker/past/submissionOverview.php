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
        <script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1.1','packages':['bar']}]}"></script>

        
        <title></title>
        <link rel="stylesheet" type="text/css" href="../css/general.css">
        <link rel="stylesheet" type="text/css" href="../css/buttons.css">
        <link rel="stylesheet" type="text/css" href="../css/insert.css">
        <link rel="stylesheet" type="text/css" href="../css/overview.css">
  
        <script src="../scripts/navigationScript.js"> </script>
        <script src="../scripts/submissionsScript.js"> </script>
    </head>
    <?php
        require_once '../classes/CitySubmission.php';
        require_once '../classes/City.php';
        require_once '../classes/Building.php';
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $view = filter_input(INPUT_GET, 'view');
    ?>
    <body>
     <div class="container" id="workspace">
       <div class="container" id="header">
           <img src="../images/sceaf_banner.jpg" id="banner" alt="Optimus Banner">
           <?php require_once '../navBar.php'; ?>
       </div>
       <div class="container" id="content">


<!--**********************************************-->
<!--***************Guide Overview*****************-->
<!--**********************************************-->

           <?php require_once 'guideOverview.php'; ?>

<!--**********************************************-->
<!--************Submission Overview***************-->
<!--**********************************************-->
            <div style="float: right; width: 100px; padding-right: 90px; margin-right: 30px">
                <a href="../insert/doFindGeneral.php?<?php echo "country=".$_SESSION['City']->getCountry()."&city=".$_SESSION['City']->getCityName();?>" class="btn btn-primary btn-md" role="button" id="changeActionsBtn" >Edit</a>  <br/>         
                <a data-toggle="modal" data-target="#submissionsModal" class="btn btn-primary btn-md" role="button" id="changeActionsBtn" >Compare</a>           
            </div>
            
              

          
                <?php 
                    if($view == 'city'){
                        require_once 'overviewCity.php'; 
                    }
                    else{
                        require_once 'overviewCategory.php'; 
                    }
                ?>
                
            <div>
                <a href="submissions.php" class="btn btn-default" role="button" id="backBtn">Back</a>
            </div>
          
       </div>
       <div class="container" id="footer">
           <p>Copyright © 2016 EPU NTUA</p>
       </div>
     </div>

  </body>
 </html>
 
 
 
 <!-- Modal -->
  <div class="modal fade" id="submissionsModal" role="dialog">
    <div class="modal-dialog" style='width:80%;'>
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Choose to Compare</h4>
        </div>
        <div class="modal-body">
          
          <div id="submissionsDiv">
          
            <table class="table table-striped table-hover"> 
                <thead>
                <tr>
                    <th hidden>ID</th>
                    <th>Submission</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Year</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <?php
                require_once("../includes/config.php");
                require_once("../includes/database.php");

                $sql = "SELECT submission.*, city.name as cityname, city.country "
                     . "FROM submission "
                     . "INNER JOIN city "
                     . "ON submission.city_id = city.id "
                     . "WHERE user_id='".$_SESSION['user']."' OR visibility='public'";

                if(!$sql = mysql_query($sql, $database->connection)){
                    //die('Ooops! We encountered a database error');
                    die("Database Selection failed:".mysql_error());
                }

                while($row = mysql_fetch_array($sql)){
                    echo '<tr class="otherSubmissions" style="cursor: pointer;">';
                    echo    '<td  class="filterable-cell" hidden id="submissionID">'.$row['id'].'</td>';
                    echo    '<td  class="filterable-cell">'. $row['name']. '</td>';
                    echo    '<td  class="filterable-cell">'. $row['cityname']. '</td>';
                    echo    '<td  class="filterable-cell">'. $row['country']. '</td>';
                    echo    '<td  class="filterable-cell">'. $row['year']. '</td>';
                    echo    '<td  class="filterable-cell">'.date('d-m-Y', strtotime($row['date'])). '</td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
          </div>
          
            
        </div>
      </div>
      
    </div>
  </div>