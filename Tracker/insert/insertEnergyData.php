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
        
        if((filter_input(INPUT_GET, 'bid')!= null)){
            /*
            ** If the data are for a building that already exists, find this building.
            ** Get all the Buildings and find the building with the specified id
            */
            $bid = filter_input(INPUT_GET, 'bid');
            $buildings = $_SESSION['CitySubmission']->getBuildings();
            for($i=0; $i<count($buildings); $i++){
                if($buildings[$i]->getId() == $bid){
                    $building = $buildings[$i];
                    break;
                }
            }
            $_SESSION['isBuildingNew'] = false;
            
            require_once("../includes/config.php");
            require_once("../includes/database.php");
            
            $sql = "SELECT * FROM building_prices WHERE building_id=".$bid." AND submission_id=".$_SESSION['CitySubmission']->getId();
            if(!$sql = mysql_query($sql, $database->connection)){
                die('Ooops! We encountered a database error');
            }

            while($row = mysql_fetch_array($sql)){
                $prices = array("electricity" => $row["electricity"], "fuel" => $row["fuel"], "naturalGas" => $row["naturalGas"], "other" => $row["other"]);
                $building->setPrices($prices);
            }


        }
        else{
            /*
            ** Else, create a new building with a negative id
            */
            $building = new Building();
            $_SESSION['insertedBuildings']++;
            $building->setId(-1 * $_SESSION['insertedBuildings']);
            $_SESSION['isBuildingNew'] = true;
        }
        /*
        ** Save the new Building in Session to add the energy data and the actions
        */
        $_SESSION['newBuilding'] = $building;
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
<form role="form" id="form" class="form-horizontal insertForm" action='doInsertEnergyData.php'  method='post'>
               <div class="dataDiv">
<!--**********************************************-->
<!--***************Building Info******************-->
<!--**********************************************-->
                    <h1>Building's Info</h1>
                    <!--
                    * Building's name Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="buildingName">Name:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="buildingName" name="buildingName" value="<?php echo $building->getName()?>" placeholder="What's the building's name?" required>
                        </div>
                    </div>
                    <!--
                    * Building's category Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="buildingCategory">Category:</label>
                        <div class="col-sm-6">
                            <select type='text' class="form-control" id='buildingCategory' name='buildingCategory' required>
                                <option selected disabled value=''>Select the building's category...</option>
                                <option <?php if($building->getType()=="administration") {echo "selected";} ?> value='administration'>Administration</option>
                                <option <?php if($building->getType()=="hospitals")      {echo "selected";} ?> value='hospitals'>Hospitals</option>
                                <option <?php if($building->getType()=="education")      {echo "selected";} ?> value='education'>Education</option>
                                <option <?php if($building->getType()=="sport")          {echo "selected";} ?> value='sport'>Sport Facilities</option>
                                <option <?php if($building->getType()=="entertainment")   {echo "selected";} ?> value='entertainment'>Entertainment</option>
                                <option <?php if($building->getType()=="other")          {echo "selected";} ?> value='other'>Other</option>
                            </select>
                        </div>
                    </div>
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
                            <input type="number" min="0" step="any" class="form-control" id="consumptionHeating" name="consumptionHeating" value="<?php echo $building->getConsumption()['heating']?>" placeholder="MWh used for heating" required/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="consumptionCooling" name="consumptionCooling" value="<?php echo $building->getConsumption()['cooling']?>" placeholder="MWh used for cooling" required/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="consumptionOther" name="consumptionOther" value="<?php echo $building->getConsumption()['other']?>" placeholder="MWh used for other" required/>
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
                            <input type="number" min="0" step="any" class="form-control" id="electricityHeating" name="electricityHeating" value="<?php if($building->getSource()['electricity']['heating']!=null) echo $building->getSource()['electricity']['heating']*100?>" placeholder="% electricity for heating" required/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="electricityCooling" name="electricityCooling" value="<?php if($building->getSource()['electricity']['heating']!=null)echo $building->getSource()['electricity']['cooling']*100?>" placeholder="% electricity for cooling" required/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="electricityOther" name="electricityOther" value="<?php if($building->getSource()['electricity']['heating']!=null)echo $building->getSource()['electricity']['other']*100?>" placeholder="% electricity for other" required/>
                        </div>   
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-sm-2">
                            Fuel:
                        </label>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="fuelHeating" name="fuelHeating" value="<?php if($building->getSource()['electricity']['heating']!=null)echo $building->getSource()['fuel']['heating']*100?>" placeholder="% fuel for heating" required/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="fuelCooling" name="fuelCooling" value="<?php if($building->getSource()['electricity']['heating']!=null)echo $building->getSource()['fuel']['cooling']*100?>" placeholder="% fuel for cooling" required/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="fuelOther" name="fuelOther" value="<?php if($building->getSource()['electricity']['heating']!=null)echo $building->getSource()['fuel']['other']*100?>" placeholder="% fuel for other" required/>
                        </div>   
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-sm-2">
                            Natural Gas:
                        </label>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="naturalGasHeating" name="naturalGasHeating" value="<?php if($building->getSource()['electricity']['heating']!=null)echo $building->getSource()['naturalGas']['heating']*100?>" placeholder="% natural gas for heating" required/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="naturalGasCooling" name="naturalGasCooling" value="<?php if($building->getSource()['electricity']['heating']!=null)echo $building->getSource()['naturalGas']['cooling']*100?>" placeholder="% natural gas for cooling" required/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="naturalGasOther" name="naturalGasOther" value="<?php if($building->getSource()['electricity']['heating']!=null)echo $building->getSource()['naturalGas']['other']*100?>" placeholder="% natural gas for other" required/>
                        </div>   
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-sm-2">
                            Other:
                        </label>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="otherHeating" name="otherHeating" value="<?php if($building->getSource()['electricity']['heating']!=null)echo $building->getSource()['other']['heating']*100?>" placeholder="% other for heating" required/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="otherCooling" name="otherCooling" value="<?php if($building->getSource()['electricity']['heating']!=null)echo $building->getSource()['other']['cooling']*100?>" placeholder="% other for cooling" required/>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="any" class="form-control" id="otherOther" name="otherOther" value="<?php if($building->getSource()['electricity']['heating']!=null)echo $building->getSource()['other']['other']*100?>" placeholder="% other for other" required/>
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
                            <input type="number" min="0" step="any" class="form-control" id="priceElectricity" name="priceElectricity" value="<?php echo $building->getPrices()['electricity']?>" placeholder="What's the price of electricity?" required>
                        </div>
                    </div>
                    <!--
                    * Fuel Price Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="priceFuel">Fuel:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="priceFuel" name="priceFuel" value="<?php echo $building->getPrices()['fuel']?>" placeholder="What's the price of fuel?" required>
                        </div>
                    </div>
                    <!--
                    * Natural Gas Price Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="priceNaturalGas">Natural Gas:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="priceNaturalGas" name="priceNaturalGas" value="<?php echo $building->getPrices()['naturalGas']?>" placeholder="What's the price of natural gas?" required>
                        </div>
                    </div>
                    <!--
                    * other Price Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="priceOther">Other:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="priceOther" name="priceOther" value="<?php echo $building->getPrices()['other']?>" placeholder="What's the price of other source?" required>
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
                            <input type="number" min="0" step="any" class="form-control" id="production" name="production" value="<?php echo $building->getProduction()?>" placeholder="MWh produced in building" required/>
                        </div>
                    </div>
               </div>
               
               <div>
                    <a href="insertBuildings.php" class="btn btn-default" role="button" id="backBtn">Back</a>
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
