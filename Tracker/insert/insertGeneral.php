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
        
        <script src="../scripts/navigationScript.js"> </script>
        <script src="../scripts/insertGeneralScript.js"> </script>
    </head>
    <?php
        require_once '../classes/CitySubmission.php';
        require_once '../classes/City.php';
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $city = $_SESSION['City'];
        $citySubmission = $_SESSION['CitySubmission'];
        
        $country = $city->getCountry();
        $year = $citySubmission->getYear();
		$baseline = $citySubmission->getBaseline();
        $targetYear = $citySubmission->getTargetYear();
        $visibility = $citySubmission->getVisibility();
        
        //echo $_SESSION['City']->getId();
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
<!--**************General Data Form***************-->
<!--**********************************************-->
           <form role="form" class="form-horizontal insertForm" action='doInsertGeneral.php'  method='post'>
               <div class="dataDiv">
<!--**********************************************-->
<!--****************General Info******************-->
<!--**********************************************-->
                    <h1>City's Info</h1>
                    <!--
                    * City's name Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="cityName">City's Name:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="cityName" name="cityName" value="<?php echo $city->getCityName()?>" placeholder="What's your city's name?" required  <?php if($_SESSION['mode']!='insert'){echo " readonly ";} ?>>
                        </div>
                    </div>
                    <!--
                    * Country Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="country">Country:</label>
                        <div class="col-sm-6">
                            <select type='text' class="form-control" id='country' name='country' required  <?php if($_SESSION['mode']!='insert'){echo " disabled ";} ?>>
                                <option selected disabled value=''>Select your country...</option>
                                <?php require_once 'inputOptionsCountry.php'; ?>
                            </select>
                        </div>
                    </div>
                    <!--
                    * Year Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="year">Input Year:</label>
                        <div class="col-sm-6">
                            <select type='number' class="form-control" id='year' name='year' required <?php if($_SESSION['mode']!='insert'){echo " disabled ";} ?>>
                                <option selected disabled value=''>Select the input year...</option>
                                <?php require 'inputOptionsYear.php'; ?>
                            </select>
                        </div>
                    </div>
                    <!--
                    * Baseline Year Input
                    
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="baseline">Baseline Year:</label>
                        <div class="col-sm-6">
                                    <select type='number' class="form-control" id='baseline' name='baseline' required >
                                        <option selected disabled value=''>Select the baseline year...</option>
                                        <?php 
                                        $foundSelected = false;
                                        for($i=0; $i<count($city->getBaselineOptions()); $i++){
                                            echo "<option value='".$city->getBaselineOptions()[$i]."'";
                                            if($city->getBaselineOptions()[$i] == $citySubmission->getBaseline()) {
                                                echo " selected ";
                                                $foundSelected = true;
                                            }
                                            echo ">".$city->getBaselineOptions()[$i]."</option>";
                                        }
                                        if($citySubmission->getYear() == null){
                                            echo "<option id='newBaseline' hidden value='".$citySubmission->getYear()."'";
                                        }
                                        else{
                                            if(in_array($citySubmission->getYear(), $city->getBaselineOptions())){
                                                echo "<option id='newBaseline' hidden value='".$citySubmission->getYear()."'";
                                            }
                                            else{
                                                echo "<option id='newBaseline'  value='".$citySubmission->getYear()."'";
                                            }
                                        }
                                        
                                        if(($foundSelected == false)&&($citySubmission->getBaseline() != null)) {
                                            echo " selected ";
                                        }
                                        echo ">".$citySubmission->getYear()."</option>";
                                        
                                        ?>
                                    </select>
                        </div>
                    </div>
					-->
					<div class="form-group">
                        <label class="control-label col-sm-2" for="baseline">Baseline Year:</label>
                        <div class="col-sm-6">
                                    <select type='number' class="form-control" id='baseline' name='baseline' required >
                                        <option selected disabled value=''>Select the baseline year...</option>
										<?php require 'inputOptionsBaselineYear.php'; ?>
                                    </select>
                        </div>
					</div>
                    <!--
                    * Target Year
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="year">Target Year:</label>
                        <div class="col-sm-6">
                            <select type='number' class="form-control" id='targetYear' name='targetYear' required>
                                <option selected disabled value=''>Select the target year...</option>
                                <?php 
                                    for($y=date("Y"); $y <= date("Y")+10; $y++){ 
                                        echo '<option ';
                                        if($targetYear == $y){
                                            echo "selected ";  
                                        }  
                                        echo 'value="';
                                        echo $y;
                                        echo '">';
                                        echo $y;
                                        echo '</option>';

                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!--
                    * Submission Visibility
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="visibility">Visibility:</label>
                        <div class="col-sm-6">
                            <select type='text' class="form-control" id='visibility' name='visibility' required>
                                <option selected disabled value=''>Select the submission's visibility...</option>
                                <option value='public'  <?php if($visibility == 'public'){ echo "selected ";}  ?>>Public</option>
                                <option value='private' <?php if($visibility == 'private'){ echo "selected ";}  ?>>Private</option>
                            </select>
                        </div>
                    </div>    
                    <!--
                    * Submission Name
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="name">Submission Name:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $citySubmission->getName()?>" placeholder="What's your submission's name?" required >
                        </div>
                    </div>    
                </div>  
<!--**********************************************-->
<!--*******************Targets********************-->
<!--**********************************************-->
                <div class="dataDiv">
                    <h1>City's Targets <small>(%) From baseline to target year </small></h1>
                    <!--
                    * Consumption Target Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="targetConsumption">Consumption Reduction:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="targetConsumption" name="targetConsumption" value="<?php if($city->getTargets()['consumption']!=null) {echo $city->getTargets()['consumption']*100;} ?>" placeholder="What's your % target for consumption reduction?" required>
                        </div>
                    </div>
                    <!--
                    * Emissions Target Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="targetEmissions">Emissions Reduction:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="targetEmissions" name="targetEmissions" value="<?php if($city->getTargets()['emissions']!=null) {echo $city->getTargets()['emissions']*100;} ?>" placeholder="What's your % target for emissions reduction?" required>
                        </div>
                    </div>
                    <!--
                    * Cost Target Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="targetCost">Cost Reduction:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="targetCost" name="targetCost" value="<?php if($city->getTargets()['cost']!=null) {echo $city->getTargets()['cost']*100;} ?>" placeholder="What's your % target for cost reduction?" required>
                        </div>
                    </div>
                    <!--
                    * RES Target Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="targetRes">RES Increase:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="targetRes" name="targetRes" value="<?php if($city->getTargets()['res']!=null) {echo $city->getTargets()['res']*100;} ?>" placeholder="What's your % target for res increase?" required>
                        </div>
                    </div>
                </div>

<!--**********************************************-->
<!--***************Emission Factors***************-->
<!--**********************************************-->
                <div class="dataDiv">
                    <h1>Standard Emission Factors <small>(tn CO₂ / MWh)</small></h1>
                    <!--
                    * Electricity Emission Factor Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="factorElectricity">Electricity:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="factorElectricity" name="factorElectricity" value="<?php echo $citySubmission->getFactors()['electricity']?>" placeholder="What's the emission factor of electricity?" required >
                        </div>
                    </div>
                    <!--
                    * Fuel Emission Factor Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="factorFuel">Fuel:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="factorFuel" name="factorFuel" value="<?php echo $citySubmission->getFactors()['fuel']?>" placeholder="What's the emission factor of fuel?" required >
                        </div>
                    </div>
                    <!--
                    * Natural Gas Emission Factor Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="factorNaturalGas">Natural Gas:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="factorNaturalGas" name="factorNaturalGas" value="<?php echo $citySubmission->getFactors()['naturalGas']?>" placeholder="What's the emission factor of natural gas?" required >
                        </div>
                    </div>
                    <!--
                    * Other Emission Factor Input
                    -->
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="factorOther">Other:</label>
                        <div class="col-sm-6">
                            <input type="number" min="0" step="any" class="form-control" id="factorOther" name="factorOther" value="<?php echo $citySubmission->getFactors()['other']?>" placeholder="What's the emission factor of other source?" required >
                        </div>
                    </div>
                </div>
                
                <div>
                    <button type="submit" class="btn btn-default" id="nextBtn">Next</button>
                    <a href="../intro.php" class="btn btn-default" role="button" id="cancelBtn">Cancel Submission</a>
                </div>
    
           </form>
       </div>
       <div class="container" id="footer">
           <p>Copyright © 2016 EPU NTUA</p>
       </div>
     </div>
    </body>
</html>
