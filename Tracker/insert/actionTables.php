<p style="display: inline">Values Selection:</p>
<select type='text' id="minmax" name="minmax">
  <option <?php if($actions != null){ if($actions[0]['minmax'] == "min"){ echo "selected"; } } ?> value="min">Min</option>
  <option <?php if($actions != null){ if($actions[0]['minmax'] == "max"){ echo "selected"; } } ?> value="max">Max</option>
</select>

<div class="dataDiv">
<!--**********************************************-->
<!--************Consumption Actions***************-->
<!--**********************************************-->
    <h1>Reduction of the Energy Consumption</h1>
    <!--
    * Building's consumption action plans
    -->
    <table class="table table-hover">
        <thead>
            <tr>
                <th hidden rowspan="2">ID</th>
                <th rowspan="2" class="col-sm-6" style="padding-bottom:25px;">Description</th>
                <th class="col-sm-1" colspan="2">Heating</th>
                <th class="col-sm-1" colspan="2">Cooling</th>
                <th class="col-sm-1" colspan="2">Other</th>
            </tr>
			<tr>
                <th class="col-sm-1" colspan="2">min - max</th>
                <th class="col-sm-1" colspan="2">min - max</th>
                <th class="col-sm-1" colspan="2">min - max</th>
            </tr>
        </thead>
        <tbody>
           <?php
            while($planrow = mysql_fetch_array($sql_consumption)){
                echo '<tr '; if(in_array($planrow['id'], $actionsIDs)){echo 'class="selected" ';} echo '>';
                echo    '<td hidden><input type="checkbox" name="consumptionPlans[]" '
                                         .'value="'.'id='.$planrow['id'].'&'
                                                   .'description='.$planrow['description'].'&' 
                                                   .'heating_min='.$planrow['heating_min'].'&'
                                                   .'heating_max='.$planrow['heating_max'].'&'
                                                   .'cooling_min='.$planrow['cooling_min'].'&'
                                                   .'cooling_max='.$planrow['cooling_max'].'&'
                                                   .'other_min='.$planrow['other_min'].'&'
                                                   .'other_max='.$planrow['other_max'].'" ';
                                           if(in_array($planrow['id'], $actionsIDs)){echo 'checked';}                           
                echo                '/>'
                        .'</td>';

                echo    '<td>'.$planrow['description'].'</td>';
                echo    '<td>'.$planrow['heating_min'].'%</td>';
                echo    '<td>'.$planrow['heating_max'].'%</td>';
                echo    '<td>'.$planrow['cooling_min'].'%</td>';
                echo    '<td>'.$planrow['cooling_max'].'%</td>';
                echo    '<td>'.$planrow['other_min'].'%</td>';
                echo    '<td>'.$planrow['other_max'].'%</td>';
                echo '</tr>';
            }
           ?>
        </tbody>
    </table>
</div>
<div class="dataDiv">
<!--**********************************************-->
<!--*************Production Actions***************-->
<!--**********************************************-->
    <h1>Increase of RES Production</h1>
    <!--
    * Building's production action plans
    -->
    <table class="table table-hover">
		<thead>
            <tr>
                <th hidden rowspan="2">ID</th>
                <th rowspan="2" class="col-sm-6" style="padding-bottom:25px;">Description</th>
                <th class="col-sm-1" colspan="2">RES</th>
                <th class="col-sm-2"></th>
            </tr>
			<tr>
                <th class="col-sm-1" colspan="2">min - max</th>
                <th class="col-sm-2"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            while($planrow = mysql_fetch_array($sql_production)){
                echo '<tr '; if(in_array($planrow['id'], $actionsIDs)){echo 'class="selected" ';} echo '>';
                echo    '<td hidden><input type="checkbox" name="productionPlans[]" '
                                         .'value="'.'id='.$planrow['id'].'&'
                                                   .'description='.$planrow['description'].'&'
                                                   .'production_min='.$planrow['res_min'].'&'
                                                   .'production_max='.$planrow['res_max'].'" ';
                                           if(in_array($planrow['id'], $actionsIDs)){echo 'checked';}                           
                echo                '/>'
                        .'</td>';

                echo    '<td>'.$planrow['description'].'</td>';
                echo    '<td>'.$planrow['res_min'].'%</td>';
                echo    '<td>'.$planrow['res_max'].'%</td>';
                echo    '<td></td>';
                echo '</tr>';
            }
           ?>
        </tbody>
    </table>
</div>
<div class="dataDiv">
<!--**********************************************-->
<!--***************Cost Actions*******************-->
<!--**********************************************-->
    <h1>Cost Reduction due to Price Optimization</h1>
    <!--
    * Building's cost action plans
    -->
    <table class="table table-hover">
        <thead>
            <tr>
                <th hidden rowspan="2">ID</th>
                <th rowspan="2" class="col-sm-6" style="padding-bottom:25px;">Description</th>
                <th class="col-sm-1" colspan="2">Cost</th>
                <th class="col-sm-2"></th>
            </tr>
			<tr>
                <th class="col-sm-1" colspan="2">min - max</th>
                <th class="col-sm-2"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            while($planrow = mysql_fetch_array($sql_cost)){
                echo '<tr '; if(in_array($planrow['id'], $actionsIDs)){echo 'class="selected" ';} echo '>';
                echo    '<td hidden><input type="checkbox" name="costPlans[]" '
                                         .'value="'.'id='.$planrow['id'].'&'
                                                   .'description='.$planrow['description'].'&'
                                                   .'cost_min='.$planrow['cost_min'].'&' 
                                                   .'cost_max='.$planrow['cost_max'].'" ';
                                           if(in_array($planrow['id'], $actionsIDs)){echo 'checked';}                           
                echo                '/>'
                        .'</td>';

                echo    '<td>'.$planrow['description'].'</td>';
                echo    '<td>'.$planrow['cost_min'].'%</td>';
                echo    '<td>'.$planrow['cost_max'].'%</td>';
                echo    '<td></td>';
                echo '</tr>';
            }
           ?>
        </tbody>
    </table>
</div>
