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



<?php
$category = $view;
$categoryResultsCurrent = $_SESSION['CitySubmission']->getResultsPercentCurrent()[$category];
$categoryResultsProjected = $_SESSION['CitySubmission']->getResultsPercentProjected()[$category];
$categoryResultsProjectedCompare = $_SESSION['CitySubmission']->getResultsPercentProjectedCompare()[$category];
?>

<script>
             
             function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Name');
                data.addColumn('number', 'Current Progress (<?php echo $_SESSION['CitySubmission']->getBaseline()."-".$_SESSION['CitySubmission']->getYear() ?>)');
                data.addColumn({type: 'string', role: 'annotation'});
                
                <?php if($categoryResultsProjectedCompare['consumption'] == null){ ?>
                    data.addColumn('number', 'Projected Progress (<?php echo $_SESSION['CitySubmission']->getBaseline()."-".($_SESSION['CitySubmission']->getYear()+1) ?>)');
                    data.addColumn({type: 'string', role: 'annotation'});
                    
                    data.addRows([
                        ['Reduction of Consumption', <?php echo ($categoryResultsCurrent['consumption']*100).",'".($categoryResultsCurrent['consumption']*100)."%'" ?>, <?php echo ($categoryResultsProjected['consumption']*100).",'".($categoryResultsProjected['consumption']*100)."%'" ?>],
                        ['Reduction of CO₂ Emissions',  <?php echo ($categoryResultsCurrent['emissions']*100).",'".($categoryResultsCurrent['emissions']*100)."%'" ?>, <?php echo ($categoryResultsProjected['emissions']*100).",'".($categoryResultsProjected['emissions']*100)."%'" ?>],
                        ['Reduction of Energy Cost',  <?php echo ($categoryResultsCurrent['cost']*100).",'".($categoryResultsCurrent['cost']*100)."%'" ?>, <?php echo ($categoryResultsProjected['cost']*100).",'".($categoryResultsProjected['cost']*100)."%'" ?>],
                        ['Increase of RES (final use)',  <?php echo ($categoryResultsCurrent['res']*100).",'".($categoryResultsCurrent['res']*100)."%'" ?>, <?php echo ($categoryResultsProjected['res']*100).",'".($categoryResultsProjected['res']*100)."%'" ?>]
                    ]);
                <?php } else{ ?>
                    data.addColumn('number', 'Projected Progress (New Actions)');
                    data.addColumn({type: 'string', role: 'annotation'});
                    data.addColumn('number', 'Projected Progress (Previous Actions)');
                    data.addColumn({type: 'string', role: 'annotation'});
                    
                    data.addRows([
                        ['Reduction of Consumption', <?php echo ($categoryResultsCurrent['consumption']*100).",'".($categoryResultsCurrent['consumption']*100)."%'" ?>, <?php echo ($categoryResultsProjected['consumption']*100).",'".($categoryResultsProjected['consumption']*100)."%'" ?>, <?php echo ($categoryResultsProjectedCompare['consumption']*100).",'".($categoryResultsProjectedCompare['consumption']*100)."%'" ?>],
                        ['Reduction of CO₂ Emissions',  <?php echo ($categoryResultsCurrent['emissions']*100).",'".($categoryResultsCurrent['emissions']*100)."%'" ?>, <?php echo ($categoryResultsProjected['emissions']*100).",'".($categoryResultsProjected['emissions']*100)."%'" ?>, <?php echo ($categoryResultsProjectedCompare['emissions']*100).",'".($categoryResultsProjectedCompare['emissions']*100)."%'" ?>],
                        ['Reduction of Energy Cost',  <?php echo ($categoryResultsCurrent['cost']*100).",'".($categoryResultsCurrent['cost']*100)."%'" ?>, <?php echo ($categoryResultsProjected['cost']*100).",'".($categoryResultsProjected['cost']*100)."%'" ?>, <?php echo ($categoryResultsProjectedCompare['cost']*100).",'".($categoryResultsProjectedCompare['cost']*100)."%'" ?>],
                        ['Increase of RES (final use)',  <?php echo ($categoryResultsCurrent['res']*100).",'".($categoryResultsCurrent['res']*100)."%'" ?>, <?php echo ($categoryResultsProjected['res']*100).",'".($categoryResultsProjected['res']*100)."%'" ?>, <?php echo ($categoryResultsProjectedCompare['res']*100).",'".($categoryResultsProjectedCompare['res']*100)."%'" ?>]
                    ]);
                <?php }  ?>
                var chart = new google.visualization.BarChart(document.getElementById('chart_div'));

                var options = {
                    
                    title: '<?php echo ucfirst($category); ?> Buildings Results',
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
                    colors: ['dodgerblue', '#054993', 'orangered'],
                    width: 930,
                    height: 700
                  };
                  
                chart.draw(data, options);
            }
            google.load("visualization", "1", {packages: ["corechart"], callback: drawChart});
        </script>
                
        
<div id="chart_div"></div>


