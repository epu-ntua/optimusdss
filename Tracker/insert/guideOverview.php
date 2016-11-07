<!DOCTYPE html>
 <div class="row">
    <div id="guideOverview">
          <div class="panel panel-default guideLabel col-sm-2">
              <div class="panel-body guideLabelBody">
                  Data Level:
              </div>
          </div>
          <div class="panel panel-defaut col-sm-3" <?php if(strpos($_SERVER['REQUEST_URI'],'city') !== false){ 
                                                            echo ' style=" background-color: #054993; "';
                                                          }
                                                    ?> >
              <div class="panel-body" >
                  <a href ="insertOverview.php?view=city" <?php if(strpos($_SERVER['REQUEST_URI'],'city') !== false){ 
                                                        echo ' style=" color: white; "';
                                                     }
                                               ?> >Municipal Buildings Sector</a>
              </div>
          </div>
        <div class="col-sm-1"></div>
          <div class="panel panel-default col-sm-2" <?php if(strpos($_SERVER['REQUEST_URI'],'city') == false){ 
                                                            echo ' style=" background-color: #054993; "';
                                                          }
                                                    ?>>
              <div class="panel-body">
                  <a href ="insertOverview.php?view=administration" <?php if(strpos($_SERVER['REQUEST_URI'],'city') == false){ 
                                                        echo ' style=" color: white; "';
                                                     }
                                               ?> >Categories</a>
              </div>
          </div>
          
    </div>
 </div>