<?php 
    $resultsCityProjected = $_SESSION['CitySubmission']->getResultsPercentProjected()['city'];
    $resultsCityProjectedCompare = $_SESSION['CitySubmission']->getResultsPercentProjectedCompare()['city'];
    $resultsCityCurrent = $_SESSION['CitySubmission']->getResultsPercentCurrent()['city'];
    $targetsCity = $_SESSION['City']->getTargets();
?>


<script>
             
             function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Name');
                data.addColumn('number', 'Target (<?php echo $_SESSION['CitySubmission']->getTargetYear() ?>)');
                data.addColumn({type: 'string', role: 'annotation'});
                data.addColumn('number', 'Current Progress (<?php echo $_SESSION['CitySubmission']->getBaseline()."-".$_SESSION['CitySubmission']->getYear() ?>)');
                data.addColumn({type: 'string', role: 'annotation'});
                
                <?php if($resultsCityProjectedCompare['consumption'] == null){ ?>
                    data.addColumn('number', 'Projected Progress (<?php echo $_SESSION['CitySubmission']->getBaseline()."-".($_SESSION['CitySubmission']->getYear()+1) ?>)');
                    data.addColumn({type: 'string', role: 'annotation'});
                    
                    data.addRows([
                        ['Reduction of Consumption', <?php echo ($targetsCity['consumption']*100).",'".($targetsCity['consumption']*100)."%'" ?>, <?php echo ($resultsCityCurrent['consumption']*100).",'".($resultsCityCurrent['consumption']*100)."%'" ?>, <?php echo ($resultsCityProjected['consumption']*100).",'".($resultsCityProjected['consumption']*100)."%'" ?>],
                        ['Reduction of CO₂ Emissions', <?php echo ($targetsCity['emissions']*100).",'".($targetsCity['emissions']*100)."%'" ?>, <?php echo ($resultsCityCurrent['emissions']*100).",'".($resultsCityCurrent['emissions']*100)."%'" ?>, <?php echo ($resultsCityProjected['emissions']*100).",'".($resultsCityProjected['emissions']*100)."%'" ?>],
                        ['Reduction of Energy Cost', <?php echo ($targetsCity['cost']*100).",'".($targetsCity['cost']*100)."%'" ?>, <?php echo ($resultsCityCurrent['cost']*100).",'".($resultsCityCurrent['cost']*100)."%'" ?>, <?php echo ($resultsCityProjected['cost']*100).",'".($resultsCityProjected['cost']*100)."%'" ?>],
                        ['Increase of RES (final use)', <?php echo ($targetsCity['res']*100).",'".($targetsCity['res']*100)."%'" ?>, <?php echo ($resultsCityCurrent['res']*100).",'".($resultsCityCurrent['res']*100)."%'" ?>, <?php echo ($resultsCityProjected['res']*100).",'".($resultsCityProjected['res']*100)."%'" ?>]
                    ]);
                <?php } else{ ?>
                    data.addColumn('number', 'Projected Progress (New Actions)');
                    data.addColumn({type: 'string', role: 'annotation'});
                    data.addColumn('number', 'Projected Progress (Previous Actions)');
                    data.addColumn({type: 'string', role: 'annotation'});
                    
                    data.addRows([
                        ['Reduction of Consumption', <?php echo ($targetsCity['consumption']*100).",'".($targetsCity['consumption']*100)."%'" ?>, <?php echo ($resultsCityCurrent['consumption']*100).",'".($resultsCityCurrent['consumption']*100)."%'" ?>, <?php echo ($resultsCityProjected['consumption']*100).",'".($resultsCityProjected['consumption']*100)."%'" ?>, <?php echo ($resultsCityProjectedCompare['consumption']*100).",'".($resultsCityProjectedCompare['consumption']*100)."%'" ?>],
                        ['Reduction of CO₂ Emissions', <?php echo ($targetsCity['emissions']*100).",'".($targetsCity['emissions']*100)."%'" ?>, <?php echo ($resultsCityCurrent['emissions']*100).",'".($resultsCityCurrent['emissions']*100)."%'" ?>, <?php echo ($resultsCityProjected['emissions']*100).",'".($resultsCityProjected['emissions']*100)."%'" ?>, <?php echo ($resultsCityProjectedCompare['emissions']*100).",'".($resultsCityProjectedCompare['emissions']*100)."%'" ?>],
                        ['Reduction of Energy Cost', <?php echo ($targetsCity['cost']*100).",'".($targetsCity['cost']*100)."%'" ?>, <?php echo ($resultsCityCurrent['cost']*100).",'".($resultsCityCurrent['cost']*100)."%'" ?>, <?php echo ($resultsCityProjected['cost']*100).",'".($resultsCityProjected['cost']*100)."%'" ?>, <?php echo ($resultsCityProjectedCompare['cost']*100).",'".($resultsCityProjectedCompare['cost']*100)."%'" ?>],
                        ['Increase of RES (final use)', <?php echo ($targetsCity['res']*100).",'".($targetsCity['res']*100)."%'" ?>, <?php echo ($resultsCityCurrent['res']*100).",'".($resultsCityCurrent['res']*100)."%'" ?>, <?php echo ($resultsCityProjected['res']*100).",'".($resultsCityProjected['res']*100)."%'" ?>, <?php echo ($resultsCityProjectedCompare['res']*100).",'".($resultsCityProjectedCompare['res']*100)."%'" ?>]
                    ]);
                <?php } ?> 
                
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


