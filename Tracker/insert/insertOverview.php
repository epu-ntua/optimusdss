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
<!--****************Guide Insert******************-->
<!--**********************************************-->

           <?php require_once 'guideInsert.php'; ?>

<!--**********************************************-->
<!--***************Guide Overview*****************-->
<!--**********************************************-->

           <?php require_once 'guideOverview.php'; ?>

<!--**********************************************-->
<!--************Submission Overview***************-->
<!--**********************************************-->
            <div>
                <a href="insertChangeActions.php?" class="btn btn-primary btn-md" role="button" id="changeActionsBtn" style="width: 150px;">Change Actions</a>           
            </div>
            
          
                <?php 
                    if($view == 'city'){
                        require_once 'overviewCity.php'; 
                    }
                    else{
                        require_once 'overviewCategory.php'; 
                    }
                ?>
                <div id="buttonDiv">
                    <a href="insertBuildings.php" class="btn btn-default" role="button" id="backBtn">Back</a>
                    <a href="doSubmission.php" class="btn btn-default" role="button" id="nextBtn">Do the Submission</a>
                    <a href="../intro.php" class="btn btn-default" role="button" id="cancelBtn">Cancel Submission</a>
                </div>
          
       </div>
       <div class="container" id="footer">
           <p>Copyright © 2016 EPU NTUA</p>
       </div>
     </div>

  </body>
 </html>