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
        
        <script src="scripts/navigationScript.js"> </script>
        <script src="../scripts/actionScript.js"> </script>
    </head>
    <?php
        require_once '../classes/CitySubmission.php';
        require_once '../classes/Building.php';
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
   
        // Get the available actions
        require_once("../includes/config.php");
        require_once("../includes/database.php");
        
        $sql_consumption = "SELECT * FROM actionplan WHERE type='consumption' AND active = 1";
        if(!$sql_consumption = mysql_query($sql_consumption, $database->connection)){
            die('Ooops! We encountered a database error');
        }
        $consumptionPlansSet = array();
        while($planrow = mysql_fetch_array($sql_consumption)){
            $consumptionPlan = array("id"=>$planrow['id'], "description"=>$planrow['description'], "heating_min"=>$planrow['heating_min'], "cooling_min"=>$planrow['cooling_min'], "other_min"=>$planrow['other_min'], "heating_max"=>$planrow['heating_max'], "cooling_max"=>$planrow['cooling_max'], "other_max"=>$planrow['other_max']);
            array_push($consumptionPlansSet, $consumptionPlan);
        }
        $sql_production = "SELECT * FROM actionplan WHERE type='production' AND active = 1";
        if(!$sql_production = mysql_query($sql_production, $database->connection)){
            die('Ooops! We encountered a database error');
        }
        $productionPlansSet = array();
        while($planrow = mysql_fetch_array($sql_production)){
            $productionPlan = array("id"=>$planrow['id'], "description"=>$planrow['description'], "res_min"=>$planrow['res_min'], "res_max"=>$planrow['res_max']);
            array_push($productionPlansSet, $productionPlan);
        }
        $sql_cost = "SELECT * FROM actionplan WHERE type='cost' AND active = 1";
        if(!$sql_cost = mysql_query($sql_cost, $database->connection)){
            die('Ooops! We encountered a database error');
        }
        $costPlansSet = array();
        while($planrow = mysql_fetch_array($sql_cost)){
            $costPlan = array("id"=>$planrow['id'], "description"=>$planrow['description'], "cost_min"=>$planrow['cost_min'], "cost_max"=>$planrow['cost_max']);
            array_push($costPlansSet, $costPlan);
        }
    ?>
    
    <body>
     <div class="container" id="workspace">
       <div class="container" id="header">
           <img src="../images/sceaf_banner.jpg" id="banner" alt="Optimus Banner">
           <?php require_once '../navBar.php'; ?>
       </div>
       <div class="container" id="content">
           <ul class="nav nav-pills">
                <li class="dropdown active">
                    <a class="dropdown-toggle" data-toggle="dropdown">Buildings<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php
                            $buildingList = $_SESSION['CitySubmission']->getBuildings();
                            for($i=0; $i<count($buildingList); $i++){
                                $building = $buildingList[$i];
                                if($building->getStatus()=="included"){
                                
                                    echo '<li><a data-toggle="pill" href="#'.$building->getName().'">'.$building->getName().'</a></li>';
                                }
                            }
                        ?>  
                    </ul>
               </li>
            </ul>

          <form role="form" class="form-horizontal insertForm" action='doChangeActions.php'  method='post'>
          <div class="tab-content">
            <?php
                $buildingList = $_SESSION['CitySubmission']->getBuildings();
                for($i=0; $i<count($buildingList); $i++){
                    $building = $buildingList[$i];
                    if($building->getStatus()=="included"){
                    
                    
                        echo '<div id="'.$building->getName().'" class="tab-pane fade">';
                    
                    echo '<h3> Change Actions for: '.$building->getName().'</h3>';


                    $actions = $building->getActions();
                    $actionsIDs = array();
                    foreach ($actions as $action) {
                        array_push($actionsIDs, $action['id']);
                    }
//                    print_r($actionsIDs);

            ?>
                <p style="display: inline">Values Selection:</p>
                <select type='text' id="<?php echo "minmax".$building->getId(); ?>" name="<?php echo "minmax".$building->getId(); ?>">
                  <option <?php if($actions != null){ if($actions[0]['minmax'] == "min"){ echo "selected"; } } ?> value="min">Min</option>
                  <option <?php if($actions != null){ if($actions[0]['minmax'] == "max"){ echo "selected"; } } ?> value="max">Max</option>
                </select>
                
                    <div class="dataDiv">
                <!--**********************************************
                    ************Consumption Actions***************
                    **********************************************-->
                        <h1>Reduction of the Energy Consumption</h1>

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th hidden>ID</th>
                                    <th class="col-sm-6">Description</th>
                                    <th class="col-sm-1" colspan="2">Heating</th>
                                    <th class="col-sm-1" colspan="2">Cooling</th>
                                    <th class="col-sm-1" colspan="2">Other</th>
                                </tr>
                            </thead>
                            <tbody>
                               <?php
                                foreach ($consumptionPlansSet as $plan) {
                                    echo '<tr '; if(in_array($plan['id'], $actionsIDs)){echo 'class="selected" ';} echo '>';
                                    echo    '<td hidden><input type="checkbox" name="consumptionPlans'.$building->getId().'[]" '
                                                                .'value="'.'id='.$plan['id'].'&'
                                                                .'description='.$plan['description'].'&' 
                                                                .'heating_min='.$plan['heating_min'].'&'
                                                                .'heating_max='.$plan['heating_max'].'&'
                                                                .'cooling_min='.$plan['cooling_min'].'&'
                                                                .'cooling_max='.$plan['cooling_max'].'&'
                                                                .'other_min='.$plan['other_min'].'&'
                                                                .'other_max='.$plan['other_max'].'" ';
                                                               if(in_array($plan['id'], $actionsIDs)){echo ' checked ';}                           
                                    echo                '/>'
                                            .'</td>';

                                    echo    '<td>'.$plan['description'].'</td>';
                                    echo    '<td>'.$plan['heating_min'].'%</td>';
                                    echo    '<td>'.$plan['heating_max'].'%</td>';
                                    echo    '<td>'.$plan['cooling_min'].'%</td>';
                                    echo    '<td>'.$plan['cooling_max'].'%</td>';
                                    echo    '<td>'.$plan['other_min'].'%</td>';
                                    echo    '<td>'.$plan['other_max'].'%</td>';
                                    echo '</tr>';
                                }
                               ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="dataDiv">
                    <!--**********************************************-->
                    <!--*************Production Actions***************-->
                    <!--**********************************************-->
                        <h1>Increase of RES Production</h1>
                        <!--
                        * Building's production action plans
                        -->
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th hidden>ID</th>
                                    <th class="col-sm-6">Description</th>
                                    <th class="col-sm-1" colspan="2">RES</th>
                                    <th class="col-sm-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($productionPlansSet as $plan) {
                                    echo '<tr '; if(in_array($plan['id'], $actionsIDs)){echo 'class="selected" ';} echo '>';
                                    echo    '<td hidden><input type="checkbox" name="productionPlans'.$building->getId().'[]" '
                                                                .'value="'.'id='.$plan['id'].'&'
                                                                .'description='.$plan['description'].'&'
                                                                .'production_min='.$plan['res_min'].'&'
                                                                .'production_max='.$plan['res_max'].'" ';
                                                               if(in_array($plan['id'], $actionsIDs)){echo ' checked ';}                           
                                    echo                '/>'
                                            .'</td>';

                                    echo    '<td>'.$plan['description'].'</td>';
                                    echo    '<td>'.$plan['res_min'].'%</td>';
                                    echo    '<td>'.$plan['res_max'].'%</td>';
                                    echo    '<td></td>';
                                    echo '</tr>';
                                }
                               ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="dataDiv">
                    <!--**********************************************-->
                    <!--***************Cost Actions*******************-->
                    <!--**********************************************-->
                        <h1>Cost Reduction due to Price Optimization</h1>
                        <!--
                        * Building's cost action plans
                        -->
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th hidden>ID</th>
                                    <th class="col-sm-6">Description</th>
                                    <th class="col-sm-1" colspan="2">Cost</th>
                                    <th class="col-sm-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($costPlansSet as $plan) {
                                    echo '<tr '; if(in_array($plan['id'], $actionsIDs)){echo 'class="selected" ';} echo '>';
                                    echo    '<td hidden><input type="checkbox" name="costPlans'.$building->getId().'[]" '
                                                                        .'value="'.'id='.$plan['id'].'&'
                                                                        .'description='.$plan['description'].'&'
                                                                        .'cost_min='.$plan['cost_min'].'&' 
                                                                        .'cost_max='.$plan['cost_max'].'" ';
                                                               if(in_array($plan['id'], $actionsIDs)){echo ' checked ';}                           
                                    echo                '/>'
                                            .'</td>';

                                    echo    '<td>'.$plan['description'].'</td>';
                                    echo    '<td>'.$plan['cost_min'].'%</td>';
                                    echo    '<td>'.$plan['cost_max'].'%</td>';
                                    echo    '<td></td>';
                                    echo '</tr>';
                                }
                               ?>
                            </tbody>
                        </table>
                    </div>
            <?php
                    echo '</div>';
                    }
                }
            ?>  

                    <div>
                        <button type="submit" class="btn btn-default" id="nextBtn">Save Actions</button>
                        <a href="insertOverview.php?view=city" class="btn btn-default" role="button" id="cancelBtn">Cancel</a>
                    </div>
          </div>
          </form>
       </div>
       <div class="container" id="footer">
           <p>Copyright © 2016 EPU NTUA</p>
       </div>
     </div>
    </body>
</html>
