<?php 
    $resultsCityProjected = $_SESSION['CitySubmission']->getResultsPercentProjected()['city'];
    $resultsCityCurrent = $_SESSION['CitySubmission']->getResultsPercentCurrent()['city'];
    $targetsCity = $_SESSION['City']->getTargets();
    
    if($_SESSION['CityCompare'] != null){
        $resultsCityCompareProjected = $_SESSION['CitySubmissionCompare']->getResultsPercentProjected()['city'];
        $resultsCityCompareCurrent = $_SESSION['CitySubmissionCompare']->getResultsPercentCurrent()['city'];
        $targetsCityCompare = $_SESSION['CityCompare']->getTargets();
        //print_r($resultsCityCompareProjected);
        //print_r($resultsCityCompareCurrent);
    }
?>


<div id="newActions" style="width: 700px; margin-top: 50px; margin-bottom: 20px;"><?php echo $_SESSION['CitySubmission']->getName().', '.$_SESSION['City']->getCityName().', '.$_SESSION['City']->getCountry().', '.$_SESSION['CitySubmission']->getYear().' (Baseline: '. $_SESSION['CitySubmission']->getBaseline().', Target: '. $_SESSION['CitySubmission']->getTargetYear().')'; ?></div>



<?php if($_SESSION['CityCompare'] != null){ ?>
<script>
        function drawChart2() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Name');
                data.addColumn('number', 'Target (<?php echo $_SESSION['CitySubmissionCompare']->getTargetYear() ?>)');
                data.addColumn({type: 'string', role: 'annotation'});
                data.addColumn('number', 'Current Progress (<?php echo $_SESSION['CitySubmissionCompare']->getBaseline()."-".$_SESSION['CitySubmissionCompare']->getYear() ?>)');
                data.addColumn({type: 'string', role: 'annotation'});
                data.addColumn('number', 'Projected Progress (<?php echo $_SESSION['CitySubmissionCompare']->getBaseline()."-".($_SESSION['CitySubmissionCompare']->getYear()+1) ?>)');
                data.addColumn({type: 'string', role: 'annotation'});

                data.addRows([
                    ['Reduction of Consumption', <?php echo ($targetsCityCompare['consumption']*100).",'".($targetsCityCompare['consumption']*100)."%'" ?>, <?php echo ($resultsCityCompareCurrent['consumption']*100).",'".($resultsCityCompareCurrent['consumption']*100)."%'" ?>, <?php echo ($resultsCityCompareProjected['consumption']*100).",'".($resultsCityCompareProjected['consumption']*100)."%'" ?>],
                    ['Reduction of CO2 Emissions', <?php echo ($targetsCityCompare['emissions']*100).",'".($targetsCityCompare['emissions']*100)."%'" ?>, <?php echo ($resultsCityCompareCurrent['emissions']*100).",'".($resultsCityCompareCurrent['emissions']*100)."%'" ?>, <?php echo ($resultsCityCompareProjected['emissions']*100).",'".($resultsCityCompareProjected['emissions']*100)."%'" ?>],
                    ['Reduction of Energy Cost', <?php echo ($targetsCityCompare['cost']*100).",'".($targetsCityCompare['cost']*100)."%'" ?>, <?php echo ($resultsCityCompareCurrent['cost']*100).",'".($resultsCityCompareCurrent['cost']*100)."%'" ?>, <?php echo ($resultsCityCompareProjected['cost']*100).",'".($resultsCityCompareProjected['cost']*100)."%'" ?>],
                    ['Increase of RES (final use)', <?php echo ($targetsCityCompare['res']*100).",'".($targetsCityCompare['res']*100)."%'" ?>, <?php echo ($resultsCityCompareCurrent['res']*100).",'".($resultsCityCompareCurrent['res']*100)."%'" ?>, <?php echo ($resultsCityCompareProjected['res']*100).",'".($resultsCityCompareProjected['res']*100)."%'" ?>]
                ]);
                
                var chart = new google.visualization.BarChart(document.getElementById('chart_div2'));

                var options = {
                    
                    title: 'Municipal Buildings Sector Results',
                    titleTextStyle: {color: '#054993', fontName: 'Arial', fontSize: '18', fontWidth: 'normal'},
                    chartArea: {top: 100, left:165, 'width': '80%'},
                    legend: {
                        position: 'top',
                        textStyle: {
                            fontSize:11
                        }
                    },
                    hAxis: {
                        title: "Results %"
                    },
                    vAxis: { 
                        title: "",
                        textStyle: {
                            fontSize:12
                        }
                    },
                    bar: { groupWidth: "55%" },
                    colors: ['dimgray','dodgerblue', 'orangered'],
                    width: 930,
                    height: 800
                  };
                  
                chart.draw(data, options);
            }
            google.load("visualization", "1", {packages: ["corechart"], callback: drawChart2});

            

</script>
<?php } ?>
<script>
             
             function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Name');
                data.addColumn('number', 'Target (<?php echo $_SESSION['CitySubmission']->getTargetYear() ?>)');
                data.addColumn({type: 'string', role: 'annotation'});
                data.addColumn('number', 'Current Progress (<?php echo $_SESSION['CitySubmission']->getBaseline()."-".$_SESSION['CitySubmission']->getYear() ?>)');
                data.addColumn({type: 'string', role: 'annotation'});
                data.addColumn('number', 'Projected Progress (<?php echo $_SESSION['CitySubmission']->getBaseline()."-".($_SESSION['CitySubmission']->getYear()+1) ?>)');
                data.addColumn({type: 'string', role: 'annotation'});

                data.addRows([
                    ['Reduction of Consumption', <?php echo ($targetsCity['consumption']*100).",'".($targetsCity['consumption']*100)."%'" ?>, <?php echo ($resultsCityCurrent['consumption']*100).",'".($resultsCityCurrent['consumption']*100)."%'" ?>, <?php echo ($resultsCityProjected['consumption']*100).",'".($resultsCityProjected['consumption']*100)."%'" ?>],
                    ['Reduction of CO₂ Emissions', <?php echo ($targetsCity['emissions']*100).",'".($targetsCity['emissions']*100)."%'" ?>, <?php echo ($resultsCityCurrent['emissions']*100).",'".($resultsCityCurrent['emissions']*100)."%'" ?>, <?php echo ($resultsCityProjected['emissions']*100).",'".($resultsCityProjected['emissions']*100)."%'" ?>],
                    ['Reduction of Energy Cost', <?php echo ($targetsCity['cost']*100).",'".($targetsCity['cost']*100)."%'" ?>, <?php echo ($resultsCityCurrent['cost']*100).",'".($resultsCityCurrent['cost']*100)."%'" ?>, <?php echo ($resultsCityProjected['cost']*100).",'".($resultsCityProjected['cost']*100)."%'" ?>],
                    ['Increase of RES (final use)', <?php echo ($targetsCity['res']*100).",'".($targetsCity['res']*100)."%'" ?>, <?php echo ($resultsCityCurrent['res']*100).",'".($resultsCityCurrent['res']*100)."%'" ?>, <?php echo ($resultsCityProjected['res']*100).",'".($resultsCityProjected['res']*100)."%'" ?>]
                ]);
                
                var chart = new google.visualization.BarChart(document.getElementById('chart_div'));

                var options = {
                    
                    title: 'Municipal Buildings Sector Results',
                    titleTextStyle: {color: '#054993', fontName: 'Arial', fontSize: '18', fontWidth: 'normal'},
                    chartArea: {top: 100, left:165, 'width': '80%'},
                    legend: {
                        position: 'top',
                        textStyle: {
                            fontSize:11
                        }
                    },
                    hAxis: {
                        title: "Results %"
                    },
                    vAxis: { 
                        title: "",
                        textStyle: {
                            fontSize:12
                        }
                    },
                    bar: { groupWidth: "55%" },
                    colors: ['dimgray','dodgerblue', '#054993', 'orangered'],
                    width: 930,
                    height: 800
                  };
                  
                chart.draw(data, options);
            }
            google.load("visualization", "1", {packages: ["corechart"], callback: drawChart});

</script>

                
        
<div id="chart_div"></div>


<?php 
    if($_SESSION['CityCompare'] != null){
?>        
        
        <div  id="newActions" style="width: 700px; margin-top: 50px; margin-bottom: 20px; background-color: orangered;"><?php echo $_SESSION['CitySubmissionCompare']->getName().', '.$_SESSION['CityCompare']->getCityName().', '.$_SESSION['CityCompare']->getCountry().', '.$_SESSION['CitySubmissionCompare']->getYear().' (Baseline: '. $_SESSION['CitySubmissionCompare']->getBaseline().', Target: '. $_SESSION['CitySubmissionCompare']->getTargetYear().')'; ?></div>
        

        <div id="chart_div2"></div>
<?php        
    }
?>


