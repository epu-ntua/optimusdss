<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        
        <link rel="stylesheet" type="text/css" href="../css/insertActions.css"> 
        <link rel="stylesheet" type="text/css" href="../css/buttons.css">
        <link rel="stylesheet" type="text/css" href="../css/insert.css">
        
        <title>Welcome</title>
        
        <script src="../scripts/adminScripts.js"></script>
    </head>
    
    <body>
        <div id="workspace">
            <div id="username">
                <a id="user"> User: <?php if (session_status() == PHP_SESSION_NONE) {
                                            session_start();
                                          } 
                                          echo $_SESSION['username'];
                                    ?>, 
                </a>
                <a id="logout" href ="<?php if((strpos($_SERVER['REQUEST_URI'],'past') != false)||(strpos($_SERVER['REQUEST_URI'],'insert') != false)||(strpos($_SERVER['REQUEST_URI'],'admin') != false)){ echo '../'; }?>doLogout.php">logout</a>
            </div>
            <form id="actionForm">
                <?php
                    require_once("../includes/config.php");
                    require_once("../includes/database.php");



                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }


                // Get the available actions
                    $sql_consumption = "SELECT * FROM actionplan WHERE type='consumption'";
                    if(!$sql_consumption = mysql_query($sql_consumption, $database->connection)){
                        die('Ooops! We encountered a database error');
                    }
                    $sql_production = "SELECT * FROM actionplan WHERE type='production'";
                    if(!$sql_production = mysql_query($sql_production, $database->connection)){
                        die('Ooops! We encountered a database error');
                    }
                    $sql_cost = "SELECT * FROM actionplan WHERE type='cost'";
                    if(!$sql_cost = mysql_query($sql_cost, $database->connection)){
                        die('Ooops! We encountered a database error');
                    }
                    

                ?>
                <h1>Action Plans</h1>
                <h1 class="actionPurpose">Reduction of the Energy Consumption</h1>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th hidden class="actionBox"></th>
                        <th class="col-sm-6">Action</th>
                        <th class="energyPurpose">Heating</th>
                        <th class="energyPurpose">Cooling</th>
                        <th class="energyPurpose">Other</th>
                        <th class="energyPurpose">Active</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        while($planrow = mysql_fetch_array($sql_consumption)){
                            echo '<tr onclick="editConsumptionAction('.$planrow['id'].')">';
                            echo    '<td hidden></td>';

                            echo    '<td class="planName">'. $planrow['description']. '</td>';
                            echo    '<td>'. $planrow['heating']. '%</td>';
                            echo    '<td>'. $planrow['cooling']. '%</td>';
                            echo    '<td>'. $planrow['other']. '%</td>';
                            echo    '<td>'. $planrow['active']. '</td>';
                            echo '</tr>';
                        }
                    ?>
                    <tbody>
                </table>
                
                <h1 class="actionPurpose">Increase of RES Production</h1>
                <table class="table table-striped table-hover"> 
                    <thead>
                    <tr>
                        <th hidden class="actionBox"></th>
                        <th class="col-sm-6">Action</th>
                        <th class="energyPurpose">Increase of RES</th>
                        <th class="energyPurpose"></th>
                        <th class="energyPurpose"></th>
                        <th class="energyPurpose">Active</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        while($planrow = mysql_fetch_array($sql_production)){
                            echo '<tr onclick="editResAction('.$planrow['id'].')">';
                            echo    '<td hidden></td>';
                            echo    '<td class="planName">'. $planrow['description']. '</td>';
                            echo    '<td>'. $planrow['res']. '%</td>';
                            echo    '<td></td>';
                            echo    '<td></td>';
                            echo    '<td>'. $planrow['active']. '</td>';
                            echo '</tr>';
                        }
                    ?>
                    
                    </tbody>
                </table>
                
                
                <h1 class="actionPurpose">Cost Reduction due to Price Optimization</h1>
                <table class="table table-striped table-hover"> 
                    <thead>
                    <tr>
                        <th hidden class="actionBox"></th>
                        <th class="col-sm-6">Action</th>
                        <th class="energyPurpose">Cost Reduction</th>
                        <th class="energyPurpose"></th>
                        <th class="energyPurpose"></th>
                        <th class="energyPurpose">Active</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        while($planrow = mysql_fetch_array($sql_cost)){
                            echo '<tr onclick="editCostAction('.$planrow['id'].')">';
                            echo    '<td hidden></td>';
                            echo    '<td class="planName">'. $planrow['description']. '</td>';
                            echo    '<td>'. $planrow['cost']. '%</td>';
                            echo    '<td></td>';
                            echo    '<td></td>';
                            echo    '<td>'. $planrow['active']. '</td>';
                            echo '</tr>';
                        }
                    ?>
                    </tbody>
                </table>
                
                
                <div id="buttons_div">
                    <button id="newActionBtn" type="button" onclick="window.location.href='addActionPlan.php'">NEW</button>
                </div>
            </form>
            
            
        </div>
    </body>
</html>



