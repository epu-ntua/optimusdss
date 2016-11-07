<!DOCTYPE html>
 <div class="row">
    <div id="guideInsert">
          <div class="panel panel-default guideLabel col-sm-2" >
              <div class="panel-body guideLabelBody">
                  Submission Steps: <?php if($_SESSION['CitySubmission']->getYear()!=null) {echo "(year: ".$_SESSION['CitySubmission']->getYear().")";} ?>
              </div>
          </div>
          <div class="panel panel-default col-sm-2" <?php if(strpos($_SERVER['REQUEST_URI'],'insertGeneral') !== false){ 
                                                            echo ' style=" background-color: #054993; "';
                                                          }
                                                    ?> >
              <div class="panel-body" >
                  <span  <?php if(strpos($_SERVER['REQUEST_URI'],'insertGeneral') !== false){ 
                                                        echo ' style=" color: white; "';
                                                     }
                                               ?> >1.General Info</span>
              </div>
          </div>
          <div class="arrow-right panel col-sm-1"></div>
          <div class="panel panel-default col-sm-2" <?php if((strpos($_SERVER['REQUEST_URI'],'insertBuildings') !== false) || (strpos($_SERVER['REQUEST_URI'],'insertEnergyData') !== false) || (strpos($_SERVER['REQUEST_URI'],'insertActions') !== false)){ 
                                                            echo ' style=" background-color: #054993; "';
                                                          }
                                                    ?> >
              <div class="panel-body">
                  <span  <?php if((strpos($_SERVER['REQUEST_URI'],'insertBuildings') !== false) || (strpos($_SERVER['REQUEST_URI'],'insertEnergyData') !== false) || (strpos($_SERVER['REQUEST_URI'],'insertActions') !== false)){ 
                                                        echo ' style=" color: white; "';
                                                     }
                                               ?> >2.Municipal Buildings</span>
              </div>
          </div>
          <div class="arrow-right panel col-sm-1"></div>
          <div class="panel panel-default col-sm-2" <?php if(strpos($_SERVER['REQUEST_URI'],'insertOverview') !== false){ 
                                                            echo ' style=" background-color: #054993; "';
                                                          }
                                                    ?>>
              <div class="panel-body">
                  <span  <?php if(strpos($_SERVER['REQUEST_URI'],'insertOverview') !== false){ 
                                                        echo ' style=" color: white; "';
                                                     }
                                               ?> >3.Overview</span>
              </div>
          </div>
    </div>
 </div>
