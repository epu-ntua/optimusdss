<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        
        
        <title></title>
        <link rel="stylesheet" type="text/css" href="css/general.css">
        
        <script src="scripts/navigationScript.js"> </script>
    </head>
    <?php
        
    ?>
    <body>
     <div class="container" id="workspace">
       <div class="container" id="header">
           <img src="images/sceaf_banner.jpg" id="banner" alt="Optimus Banner">
           <?php require_once 'navBar.php'; ?>
       </div>
       <div class="container" id="content">
           <h1>
               Welcome to Optimus City Progress Tracker
           </h1>
           
           <p>
               This tool helps you evaluate the energy progress of your municipal buildings.
               You can either make a new data input of a new or an existing city, or see your 
               previous submissions.
           </p>
           <div class="panel panel-default">
               <div class="panel-body">
                   <p>Insert new data</p>
                   <a href="insert/doInitializationInsert.php" class="btn btn-primary" role="button">New City Submission</a>
               </div>
           </div>
           <div class="panel panel-default">
               <div class="panel-body">
                   <p>...or see your past submissions</p>
                   <a href="past/submissions.php" class="btn btn-primary" role="button">Past Submissions</a>
               </div>
           </div>
       </div>
       <div class="container" id="footer">
           <p> Copyright © 2016 EPU NTUA</p>
       </div>
     </div>
    </body>
</html>
