<?php 
    $resultsCityProjected = $_SESSION['CitySubmission']->getResultsProjected();
    $resultsCityProjectedCompare = $_SESSION['CitySubmission']->getResultsProjectedCompare();
    $resultsCityCurrent = $_SESSION['CitySubmission']->getResultsCurrent();
    $targetsCity = $_SESSION['City']->getTargets();
?>

<div id="guideCategory">
    <ul>
            <li><a href ="insertOverview.php?view=administration" 
                    <?php if(strpos($_SERVER['REQUEST_URI'],'administration') !== false){ 
                            echo ' style="color:teal; font-weight: bold; text-decoration: underline;"';
                          }
                    ?> >Administration</a></li>
            <li><a href ="insertOverview.php?view=hospitals"
                    <?php if(strpos($_SERVER['REQUEST_URI'],'hospitals') !== false){ 
                            echo ' style="color:teal; font-weight: bold; text-decoration: underline;"';
                          }
                    ?> >Hospitals</a></li>
            <li><a href ="insertOverview.php?view=education"
                    <?php if(strpos($_SERVER['REQUEST_URI'],'education') !== false){ 
                            echo ' style="color:teal; font-weight: bold; text-decoration: underline;"';
                          }
                    ?> >Education</a></li>
            <li><a href ="insertOverview.php?view=sport"
                    <?php if(strpos($_SERVER['REQUEST_URI'],'sport') !== false){ 
                            echo ' style="color:teal; font-weight: bold; text-decoration: underline;"';
                          }
                    ?> >Sport Facilities</a></li>
            <li><a href ="insertOverview.php?view=entertainment"
                    <?php if(strpos($_SERVER['REQUEST_URI'],'entertainment') !== false){ 
                            echo ' style="color:teal; font-weight: bold; text-decoration: underline;"';
                          }
                    ?> >Entertainment</a></li>
            <li><a href ="insertOverview.php?view=other"
                    <?php if(strpos($_SERVER['REQUEST_URI'],'other') !== false){ 
                            echo ' style="color:teal; font-weight: bold; text-decoration: underline;"';
                          }
                    ?> >Other</a></li>
    </ul> 
</div>

<div class="dataDiv">
    <h1>Reduction of Energy Consumption</h1>
    
    <div>
        <span>Current Progress (2012-2015)</span> 
        <div class="progress current-progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $resultsCityCurrent['consumption']*100 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $resultsCityCurrent['consumption']*100 ?>%">
          </div>
            <?php echo $resultsCityCurrent['consumption']*100 ?>%
        </div>
    </div>
    <div>
        <span>Projected Progress (2012-2016)</span> 
        <div class="progress projected-progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $resultsCityProjected['consumption']*100 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $resultsCityProjected['consumption']*100 ?>%">
          </div>
            <?php echo $resultsCityProjected['consumption']*100 ?>%
        </div>
    </div>
    <div>
        <div class="progress projected-progressCompare" <?php if($resultsCityProjectedCompare['consumption'] == null){echo " hidden ";} ?>>
          <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $resultsCityProjectedCompare['consumption']*100 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $resultsCityProjectedCompare['consumption']*100 ?>%">
          </div>
            <?php echo $resultsCityProjectedCompare['consumption']*100 ?>%
        </div>
    </div>
</div>


<div class="dataDiv">
    <h1>Reduction of CO₂ Emissions </h1>
    

    <div>
        <span>Current Progress (2012-2015)</span> 
        <div class="progress current-progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $resultsCityCurrent['emissions']*100 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $resultsCityCurrent['emissions']*100 ?>%">
          </div>
            <?php echo $resultsCityCurrent['emissions']*100 ?>%
        </div>
    </div>
    <div>
        <span>Projected Progress (2012-2016)</span> 
        <div class="progress projected-progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $resultsCityProjected['emissions']*100 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $resultsCityProjected['emissions']*100 ?>%">
          </div>
            <?php echo $resultsCityProjected['emissions']*100 ?>%
        </div>
    </div>
    <div>
        <div class="progress projected-progressCompare" <?php if($resultsCityProjectedCompare['emissions'] == null){echo " hidden ";} ?>>
          <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $resultsCityProjectedCompare['emissions']*100 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $resultsCityProjectedCompare['emissions']*100 ?>%">
          </div>
            <?php echo $resultsCityProjectedCompare['emissions']*100 ?>%
        </div>
    </div>
</div>


<div class="dataDiv">
    <h1>Energy cost reduction </h1>
    

    <div>
        <span>Current Progress (2012-2015)</span> 
        <div class="progress current-progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $resultsCityCurrent['cost']*100 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $resultsCityCurrent['cost']*100 ?>%">
          </div>
            <?php echo $resultsCityCurrent['cost']*100 ?>%
        </div>
    </div>
    <div>
        <span>Projected Progress (2012-2016)</span> 
        <div class="progress projected-progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $resultsCityProjected['cost']*100 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $resultsCityProjected['cost']*100 ?>%">
          </div>
            <?php echo $resultsCityProjected['cost']*100 ?>%
        </div>
    </div>
    <div>
        <div class="progress projected-progressCompare" <?php if($resultsCityProjectedCompare['cost'] == null){echo " hidden ";} ?>>
          <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $resultsCityProjectedCompare['cost']*100 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $resultsCityProjectedCompare['cost']*100 ?>%">
          </div>
            <?php echo $resultsCityProjectedCompare['cost']*100 ?>%
        </div>
    </div>
</div>


<div class="dataDiv">
    <h1>Increase of RES in the final use</h1>
    

    <div>
        <span>Current Progress (2012-2015)</span> 
        <div class="progress current-progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $resultsCityCurrent['res']*100 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $resultsCityCurrent['res']*100 ?>%">
          </div>
            <?php echo $resultsCityCurrent['res']*100 ?>%
        </div>
    </div>
    <div>
        <span>Projected Progress (2012-2016)</span> 
        <div class="progress projected-progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $resultsCityProjected['res']*100 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $resultsCityProjected['res']*100 ?>%">
          </div>
            <?php echo $resultsCityProjected['res']*100 ?>%
        </div>
    </div>
    <div>
        <div class="progress projected-progressCompare" <?php if($resultsCityProjectedCompare['res'] == null){echo " hidden ";} ?>>
          <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $resultsCityProjectedCompare['res']*100 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $resultsCityProjectedCompare['res']*100 ?>%">
          </div>
            <?php echo $resultsCityProjectedCompare['res']*100 ?>%
        </div>
    </div>
</div>