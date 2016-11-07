<div id="guideCategory">
    <ul>
            <li><a href ="submissionOverview.php?view=administration" 
                    <?php if(strpos($_SERVER['REQUEST_URI'],'administration') !== false){ 
                            echo ' style="color:teal; font-weight: bold; text-decoration: underline;"';
                          }
                    ?> >Administration</a></li>
            <li><a href ="submissionOverview.php?view=hospitals"
                    <?php if(strpos($_SERVER['REQUEST_URI'],'hospitals') !== false){ 
                            echo ' style="color:teal; font-weight: bold; text-decoration: underline;"';
                          }
                    ?> >Hospitals</a></li>
            <li><a href ="submissionOverview.php?view=education"
                    <?php if(strpos($_SERVER['REQUEST_URI'],'education') !== false){ 
                            echo ' style="color:teal; font-weight: bold; text-decoration: underline;"';
                          }
                    ?> >Education</a></li>
            <li><a href ="submissionOverview.php?view=sport"
                    <?php if(strpos($_SERVER['REQUEST_URI'],'sport') !== false){ 
                            echo ' style="color:teal; font-weight: bold; text-decoration: underline;"';
                          }
                    ?> >Sport Facilities</a></li>
            <li><a href ="submissionOverview.php?view=entertainment"
                    <?php if(strpos($_SERVER['REQUEST_URI'],'entertainment') !== false){ 
                            echo ' style="color:teal; font-weight: bold; text-decoration: underline;"';
                          }
                    ?> >Entertainment</a></li>
            <li><a href ="submissionOverview.php?view=other"
                    <?php if(strpos($_SERVER['REQUEST_URI'],'other') !== false){ 
                            echo ' style="color:teal; font-weight: bold; text-decoration: underline;"';
                          }
                    ?> >Other</a></li>
    </ul> 
</div>

<div id="newActions" style="width: 700px; margin-top: 50px; margin-bottom: 20px;"><?php echo $_SESSION['City']->getCityName().', '.$_SESSION['City']->getCountry().', '.$_SESSION['CitySubmission']->getYear().' (Baseline: '. $_SESSION['CitySubmission']->getBaseline().', Target: '. $_SESSION['CitySubmission']->getTargetYear().')'; ?></div>


<?php
    $category = $view;
    $categoryResultsCurrent = $_SESSION['CitySubmission']->getResultsPercentCurrent()[$category];
    $categoryResultsProjected = $_SESSION['CitySubmission']->getResultsPercentProjected()[$category];
    $categoryResultsProjectedCompare = $_SESSION['CitySubmission']->getResultsPercentProjectedCompare()[$category];

    if($_SESSION['CityCompare'] != null){
        $category = $view;
        $categoryCompareResultsCurrent = $_SESSION['CitySubmissionCompare']->getResultsPercentCurrent()[$category];
        $categoryCompareResultsProjected = $_SESSION['CitySubmissionCompare']->getResultsPercentProjected()[$category];
        $categoryCompareResultsProjectedCompare = $_SESSION['CitySubmissionCompare']->getResultsPercentProjectedCompare()[$category];
    }
?>

<?php if($_SESSION['CityCompare'] != null){ ?>
<script>
        function drawChart2() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Name');
                data.addColumn('number', 'Current Progress (<?php echo $_SESSION['CitySubmissionCompare']->getBaseline()."-".$_SESSION['CitySubmissionCompare']->getYear() ?>)');
                data.addColumn({type: 'string', role: 'annotation'});
                data.addColumn('number', 'Projected Progress (<?php echo $_SESSION['CitySubmissionCompare']->getBaseline()."-".($_SESSION['CitySubmissionCompare']->getYear()+1) ?>)');
                data.addColumn({type: 'string', role: 'annotation'});

                data.addRows([
                    ['Reduction of Consumption', <?php echo ($categoryCompareResultsCurrent['consumption']*100).",'".($categoryCompareResultsCurrent['consumption']*100)."%'" ?>, <?php echo ($categoryCompareResultsProjected['consumption']*100).",'".($categoryCompareResultsProjected['consumption']*100)."%'" ?>],
                    ['Reduction of CO₂ Emissions',  <?php echo ($categoryCompareResultsCurrent['emissions']*100).",'".($categoryCompareResultsCurrent['emissions']*100)."%'" ?>, <?php echo ($categoryCompareResultsProjected['emissions']*100).",'".($categoryCompareResultsProjected['emissions']*100)."%'" ?>],
                    ['Reduction of Energy Cost',  <?php echo ($categoryCompareResultsCurrent['cost']*100).",'".($categoryCompareResultsCurrent['cost']*100)."%'" ?>, <?php echo ($categoryCompareResultsProjected['cost']*100).",'".($categoryCompareResultsProjected['cost']*100)."%'" ?>],
                    ['Increase of RES (final use)',  <?php echo ($categoryCompareResultsCurrent['res']*100).",'".($categoryCompareResultsCurrent['res']*100)."%'" ?>, <?php echo ($categoryCompareResultsProjected['res']*100).",'".($categoryCompareResultsProjected['res']*100)."%'" ?>]
                ]);
                    
                    
                var chart = new google.visualization.BarChart(document.getElementById('chart_div2'));

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
                    colors: ['dodgerblue', 'orangered'],
                    width: 930,
                    height: 700
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
                data.addColumn('number', 'Current Progress (<?php echo $_SESSION['CitySubmission']->getBaseline()."-".$_SESSION['CitySubmission']->getYear() ?>)');
                data.addColumn({type: 'string', role: 'annotation'});
                data.addColumn('number', 'Projected Progress (<?php echo $_SESSION['CitySubmission']->getBaseline()."-".($_SESSION['CitySubmission']->getYear()+1) ?>)');
                data.addColumn({type: 'string', role: 'annotation'});

                data.addRows([
                    ['Reduction of Consumption', <?php echo ($categoryResultsCurrent['consumption']*100).",'".($categoryResultsCurrent['consumption']*100)."%'" ?>, <?php echo ($categoryResultsProjected['consumption']*100).",'".($categoryResultsProjected['consumption']*100)."%'" ?>],
                    ['Reduction of CO₂ Emissions',  <?php echo ($categoryResultsCurrent['emissions']*100).",'".($categoryResultsCurrent['emissions']*100)."%'" ?>, <?php echo ($categoryResultsProjected['emissions']*100).",'".($categoryResultsProjected['emissions']*100)."%'" ?>],
                    ['Reduction of Energy Cost',  <?php echo ($categoryResultsCurrent['cost']*100).",'".($categoryResultsCurrent['cost']*100)."%'" ?>, <?php echo ($categoryResultsProjected['cost']*100).",'".($categoryResultsProjected['cost']*100)."%'" ?>],
                    ['Increase of RES (final use)',  <?php echo ($categoryResultsCurrent['res']*100).",'".($categoryResultsCurrent['res']*100)."%'" ?>, <?php echo ($categoryResultsProjected['res']*100).",'".($categoryResultsProjected['res']*100)."%'" ?>]
                ]);
                    
                    
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


<?php 
    if($_SESSION['CityCompare'] != null){
?>        
       
        <div  id="newActions" style="width: 700px; margin-top: 50px; margin-bottom: 20px; background-color: orangered;"><?php echo $_SESSION['CityCompare']->getCityName().', '.$_SESSION['CityCompare']->getCountry().', '.$_SESSION['CitySubmissionCompare']->getYear().' (Baseline: '. $_SESSION['CitySubmissionCompare']->getBaseline().', Target: '. $_SESSION['CitySubmissionCompare']->getTargetYear().')'; ?></div>


        <div id="chart_div2"></div>
<?php        
    }
?>

