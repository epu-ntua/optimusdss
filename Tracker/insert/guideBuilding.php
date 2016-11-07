<!DOCTYPE html>

<?php
    require_once '../classes/Building.php';
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $building = $_SESSION['newBuilding'];  
    $bid = $building->getId();
?>
 <div class="row">
    <div id="guideBuilding">
          <div class="panel panel-default guideLabel col-sm-2">
              <div class="panel-body guideLabelBody">
                  New Building:
              </div>
          </div>
          <div class="panel panel-defaut col-sm-2" <?php if(strpos($_SERVER['REQUEST_URI'],'insertEnergyData') !== false){ 
                                                            echo ' style=" background-color: #054993; "';
                                                          }
                                                    ?>>
              <div class="panel-body">
                  <span  <?php if(strpos($_SERVER['REQUEST_URI'],'insertEnergyData') !== false){ 
                                                        echo ' style=" color: white; "';
                                                     }
                                               ?>>Current Energy Data</span>
              </div>
          </div>
          <div class="arrow-right panel col-sm-2" ></div>
          <div class="panel panel-defaut col-sm-2" <?php if(strpos($_SERVER['REQUEST_URI'],'insertBaselineData') !== false){ 
                                                            echo ' style=" background-color: #054993; "';
                                                          }
                                                    ?>>
              <div class="panel-body">
                  <span  <?php if(strpos($_SERVER['REQUEST_URI'],'insertBaselineData') !== false){ 
                                                        echo ' style=" color: white; "';
                                                     }
                                               ?>>Baseline Energy Data</span>
              </div>
          </div>
          <div class="arrow-right panel col-sm-2" ></div>
          <div class="panel panel-default col-sm-2" <?php if(strpos($_SERVER['REQUEST_URI'],'insertActions') !== false){ 
                                                            echo ' style=" background-color: #054993; "';
                                                          }
                                                    ?>>
              <div class="panel-body">
                  <span  <?php if(strpos($_SERVER['REQUEST_URI'],'insertActions') !== false){ 
                                                        echo ' style=" color: white; "';
                                                     }
                                               ?>>Action Plans</span>
              </div>
          </div>
          <div class="col-sm-2"></div>
    </div>
 </div>
