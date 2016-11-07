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
        <link rel="stylesheet" type="text/css" href="../css/insert.css">
        <link rel="stylesheet" type="text/css" href="../css/buttons.css">
        
        <script src="../scripts/navigationScript.js"> </script>
        <script src="../scripts/insertEnergyDataScript.js"> </script>
    </head>
    <?php
        require_once '../classes/CitySubmission.php';
        require_once '../classes/Building.php';
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
          
        $building = $_SESSION['newBuilding'] ;
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
<!--************Building Energy Form**************-->
<!--**********************************************-->
<form role="form" id="form" class="form-horizontal insertForm" action='doInsertBaselineData.php'  method='post'>
               <div class="dataDiv">
<!--**********************************************-->
<!--***************Building Info******************-->
<!--**********************************************-->
                    <h1>Baseline Year <?php echo $_SESSION['CitySubmission']->getBaseline(); ?></h1>
                    
               </div>
               <div class="dataDiv">
<!--**********************************************-->
<!--***************Consumption Data***************-->
<!--**********************************************-->
                    <h1>Consumption Data <small>(MWh)</small></h1>
                    <!--
                    * Building's Consumption Inputs
                    -->
                    
                    <div class="row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-3">Heating</div>
                        <div class="col-sm-3">Cooling</div>
                        <div class="col-sm-3">Other</div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-sm-2">
                            Consumption:
                        </label>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="consumptionHeating" name="consumptionHeating" value="<?php echo $building->getConsumptionBaseline()['heating']?>" placeholder="MWh used for heating" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="consumptionCooling" name="consumptionCooling" value="<?php echo $building->getConsumptionBaseline()['cooling']?>" placeholder="MWh used for cooling" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="consumptionOther" name="consumptionOther" value="<?php echo $building->getConsumptionBaseline()['other']?>" placeholder="MWh used for other" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>   
                    </div>
               </div>
               <div  class="dataDiv">
<!--**********************************************-->
<!--*****************Source Usage*****************-->
<!--**********************************************-->
                    <h1>Source Usage <small>(%)</small></h1>
                    <!--
                    * Source Usage Inputs
                    -->
                    
                    <div class="row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-3">Heating</div>
                        <div class="col-sm-3">Cooling</div>
                        <div class="col-sm-3">Other</div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-sm-2">
                            Electricity:
                        </label>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="electricityHeating" name="electricityHeating" value="<?php if($building->getSourceBaseline()['electricity']['heating'] >= 0)echo $building->getSourceBaseline()['electricity']['heating']*100; ?>" placeholder="% electricity for heating" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="electricityCooling" name="electricityCooling" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0)echo $building->getSourceBaseline()['electricity']['cooling']*100?>" placeholder="% electricity for cooling" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="electricityOther" name="electricityOther" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0)echo $building->getSourceBaseline()['electricity']['other']*100?>" placeholder="% electricity for other" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>   
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-sm-2">
                            Fuel:
                        </label>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="fuelHeating" name="fuelHeating" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0)echo $building->getSourceBaseline()['fuel']['heating']*100?>" placeholder="% fuel for heating" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="fuelCooling" name="fuelCooling" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0)echo $building->getSourceBaseline()['fuel']['cooling']*100?>" placeholder="% fuel for cooling" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="fuelOther" name="fuelOther" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0)echo $building->getSourceBaseline()['fuel']['other']*100?>" placeholder="% fuel for other" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>   
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-sm-2">
                            Natural Gas:
                        </label>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="naturalGasHeating" name="naturalGasHeating" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0)echo $building->getSourceBaseline()['naturalGas']['heating']*100?>" placeholder="% natural gas for heating" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="naturalGasCooling" name="naturalGasCooling" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0)echo $building->getSourceBaseline()['naturalGas']['cooling']*100?>" placeholder="% natural gas for cooling" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="naturalGasOther" name="naturalGasOther" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0)echo $building->getSourceBaseline()['naturalGas']['other']*100?>" placeholder="% natural gas for other" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>   
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-sm-2">
                            Other:
                        </label>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="otherHeating" name="otherHeating" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0)echo $building->getSourceBaseline()['other']['heating']*100?>" placeholder="% other for heating" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="otherCooling" name="otherCooling" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0)echo $building->getSourceBaseline()['other']['cooling']*100?>" placeholder="% other for cooling" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="otherOther" name="otherOther" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0)echo $building->getSourceBaseline()['other']['other']*100?>" placeholder="% other for other" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>/>
                        </div>   
                    </div>
               </div>
 
<!--**********************************************-->
<!--********************Prices********************-->
<!--**********************************************-->
                <div class="dataDiv">
                    <h1>Energy Prices <small>(€ / MWh)</small></h1>
                    <!--
                    * Electricity Price Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="priceElectricity">Electricity:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="priceElectricity" name="priceElectricity" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0) echo $building->getPricesBaseline()['electricity']; ?>" placeholder="What's the price of electricity?" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>>
                        </div>
                    </div>
                    <!--
                    * Fuel Price Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="priceFuel">Fuel:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="priceFuel" name="priceFuel" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0) echo $building->getPricesBaseline()['fuel']; ?>" placeholder="What's the price of fuel?" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>>
                        </div>
                    </div>
                    <!--
                    * Natural Gas Price Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="priceNaturalGas">Natural Gas:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="priceNaturalGas" name="priceNaturalGas" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0) echo $building->getPricesBaseline()['naturalGas']; ?>" placeholder="What's the price of natural gas?" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>>
                        </div>
                    </div>
                    <!--
                    * other Price Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="priceOther">Other:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="priceOther" name="priceOther" value="<?php if($building->getSourceBaseline()['electricity']['heating']>= 0) echo $building->getPricesBaseline()['other']; ?>" placeholder="What's the price of other source?" required <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?>>
                        </div>
                    </div>
                </div> 

               <div class="dataDiv">
<!--**********************************************-->
<!--****************Production Data***************-->
<!--**********************************************-->
                    <h1>RES Production Data <small>(MWh)</small></h1>
                    <!--
                    * Building's RES Production Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="production">RES Production:</label>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="production" name="production" value="<?php echo $building->getProductionBaseline()?>" placeholder="MWh produced in building" required  <?php if($building->getConsumptionBaseline()['heating']!=null) {echo 'readonly';} ?> />
                        </div>
                    </div>
               </div>
               
               <div>
                    <a href="insertEnergyData.php?bid=<?php echo $building->getId(); ?>"  class="btn btn-default" role="button" id="backBtn">Back</a>
                    <button type="submit" class="btn btn-default" id="nextBtn">Next</button>
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
