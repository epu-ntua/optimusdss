<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="../css/NavBar.css"> 
        <link rel="stylesheet" type="text/css" href="../css/index.css">
        <link rel="stylesheet" type="text/css" href="../css/buttons.css">
        <link rel="stylesheet" type="text/css" href="../css/registration.css"> 
        <link rel="stylesheet" type="text/css" href="../css/newActions.css"> 
        <title>Welcome</title>
        
        <script src="../scripts/adminScripts.js"></script>
        <script src="../scripts/inputValidation.js"></script>
    </head>
    <body>
      <?php
        $action_id = $_GET['id'];
        $action_type = $_GET['type'];
        require_once("../includes/config.php");
        require_once("../includes/database.php");

        $sql = "SELECT * FROM actionplan WHERE id=".$action_id;
        if(!$sql = mysql_query($sql, $database->connection)){
            die('Ooops! We encountered a database error');
        }
        
        while($planrow = mysql_fetch_array($sql)){
            $description = trim($planrow['description']);
            $type = $planrow['type'];
            $active = $planrow['active'];
            $active++;
            $reductionHeating = $planrow['heating'];
            $reductionCooling = $planrow['cooling'];
            $reductionOther = $planrow['other'];
            $reductionCost = $planrow['cost'];
            $resProduction = $planrow['res'];
        }
      ?>
      <div id="workspace">
        <div id="username">
            <a id="user"> User: <?php if (session_status() == PHP_SESSION_NONE) {
                                        session_start();
                                      } 
                                      echo $_SESSION['username'];
                                ?>, 
            </a>
            <a id="logout" href ="<?php if((strpos($_SERVER['REQUEST_URI'],'past') != false)||(strpos($_SERVER['REQUEST_URI'],'insert') != false)||(strpos($_SERVER['REQUEST_URI'],'admin') != false)){ echo '../'; }?>doLogout.php">logout</a>
        </div>
          
        <h1>New Action Plan  </h1>
        <?php
          
          if (isset($_POST['formsubmitted'])) {
    
                require_once("../includes/config.php");
                require_once("../includes/database.php");

                $dbc = $database->connection;

                $error = array(); //Declare An Array to store any error message
                if (empty($_POST['description'])) { //if no name has been supplied
                    $error[] = 'Please Enter a description '; //add to array "error"
                } else {
                    $description = $_POST['description']; //else assign it a variable
                }
                
                if (empty($_POST['type'])) { //if no name has been supplied
                    $error[] = 'Please Enter an action type '; //add to array "error"
                } else {
                    $type = $_POST['type']; //else assign it a variable
                }
                if (empty($_POST['active'])) { //if no name has been supplied
                    $error[] = 'Please Enter if active '; //add to array "error"
                } else {
                    $active = $_POST['active'] - 1; //else assign it a variable
                }
                
                
                if($type=='consumption'){
                    if (empty($_POST['reductionHeating'])) { //if no name has been supplied
                        $error[] = 'Please Enter a the heating reduction '; //add to array "error"
                    } else {
                        $reductionHeating = $_POST['reductionHeating']; //else assign it a variable
                    }
                    
                    if (empty($_POST['reductionCooling'])) { //if no name has been supplied
                        $error[] = 'Please Enter a the cooling reduction '; //add to array "error"
                    } else {
                        $reductionCooling = $_POST['reductionCooling']; //else assign it a variable
                    }
                    
                    if (empty($_POST['reductionOther'])) { //if no name has been supplied
                        $error[] = 'Please Enter a the other reduction '; //add to array "error"
                    } else {
                        $reductionOther = $_POST['reductionOther']; //else assign it a variable
                    }
                }
                if($type=='production'){    
                    if (empty($_POST['resProduction'])) { //if no name has been supplied
                        $error[] = 'Please Enter a the RES production '; //add to array "error"
                    } else {
                        $resProduction = $_POST['resProduction']; //else assign it a variable
                    }
                }   
                if($type=='cost'){      
                    if (empty($_POST['reductionCost'])) { //if no name has been supplied
                        $error[] = 'Please Enter a the cost reduction '; //add to array "error"
                    } else {
                        $reductionCost = $_POST['reductionCost']; //else assign it a variable
                    }
                }
                

                if (empty($error)) //send to Database if there's no error '

                { // If everything's OK...
                    
                    if($type=='consumption'){
                        $query_insert_user =
                            
                            "UPDATE actionplan SET description='".trim($description)."', type='consumption', heating='".$reductionHeating."', cooling='".$reductionCooling."', other='".$reductionOther."', res='0', cost='0', active='".$active."' WHERE id=".$action_id;
                    }
                    if($type=='production'){
                        $query_insert_user =
                            "UPDATE actionplan SET description='".trim($description)."', type='production', heating='0', cooling='0', other='0', res='".$resProduction."', cost='0', active='".$active."' WHERE id=".$action_id;

                    }
                    if($type=='cost'){
                        $query_insert_user =
                            "UPDATE actionplan SET description='".trim($description)."', type='cost', heating='0', cooling='0', other='0', res='0', cost='".$reductionCost."', active='".$active."' WHERE id=".$action_id;

                    }
                    
//                    echo "<div> '$query_insert_user' </div>";

                    $result_insert_user = mysql_query( $query_insert_user, $dbc);
                    if (!$result_insert_user) {
                        echo 'Query Failed ';
                    }

                    if (mysql_affected_rows($dbc) == 1) { //If the Insert Query was successfull.
                        echo '<div class="success">Added Successfully! <a href="actionplans.php" > Back </a>  </div>';

                    } else { // If it did not run OK.
                        echo '<div class="errormsgbox">You could not be registered due to a system error. We apologize for any inconvenience.</div>';
                    }
                } 
                else{ //If the "error" array contains error msg , display them
                    echo '<div class="errormsgbox"> <ol>';
                    foreach ($error as $key => $values) {
                        echo '	<li>' . $values . '</li>';
                    }
                    echo '</ol></div>';
                }
                mysql_close($dbc); //Close the DB Connection
            }
        ?>
        
            <form action="" method="post" >
                <div id="registrationInputs">
                    <table style="margin-left: 30px;">
                        <tr>
                            <td><label for="description">Description :</label></td>
                            <td><textarea id="description" name="description" cols="50" rows="3"> <?php echo trim($description);?> </textarea></td>
                        </tr>

                        <tr>
                            <td><label for="type">Type :</label></td>
                            <td>
                                <select name='type' id="type" onchange="changeType(this);">
                                    <option value='consumption' <?php if($type=="consumption") echo " selected " ?>>consumption</option>
                                    <option value='production' <?php if($type=="production") echo " selected " ?>>production</option>
                                    <option value='cost' <?php if($type=="cost") echo " selected " ?>>cost</option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr  <?php if($type!="consumption") echo " hidden " ?> id="heatingRow">
                            <td><label for="reductionHeating">Reduction Heating (%):</label></td>
                            <td><input type="number" min="0" max="100" id="reductionHeating" name="reductionHeating" size="25"  onfocus="this.oldvalue = this.value;" onchange="actionValidation(this);" value="<?php echo $reductionHeating ?>"/></td>
                        </tr>

                        <tr  <?php if($type!="consumption") echo " hidden " ?> id="coolingRow">
                            <td><label for="reductionCooling">Reduction Cooling (%):</label></td>
                            <td><input type="number" min="0" max="100" id="reductionCooling" name="reductionCooling" size="25"  onfocus="this.oldvalue = this.value;" onchange="actionValidation(this);" value="<?php echo $reductionCooling ?>"/></td>
                        </tr>

                        <tr  <?php if($type!="consumption") echo " hidden " ?> id="otherRow">
                            <td><label for="reductionOther">Reduction Other (%):</label></td>
                            <td><input type="number" min="0" max="100" id="reductionOther" name="reductionOther" size="25"  onfocus="this.oldvalue = this.value;" onchange="actionValidation(this);" value="<?php echo $reductionOther ?>"/></td>
                        </tr>

                        <tr  <?php if($type!="production") echo " hidden " ?> id="resRow">
                            <td><label for="resProduction">RES Production (%):</label></td>
                            <td><input type="number" min="0" max="100" id="resProduction" name="resProduction" size="25"  onfocus="this.oldvalue = this.value;" onchange="actionValidation(this);" value="<?php echo $resProduction ?>"/></td>
                        </tr>

                      <tr  <?php if($type!="cost") echo " hidden " ?> id="costRow">
                            <td><label for="reductionCost">Reduction Cost (%):</label></td>
                            <td><input type="number" min="0" max="100" id="reductionCost" name="reductionCost" size="25"  onfocus="this.oldvalue = this.value;" onchange="actionValidation(this);" value="<?php echo $reductionCost ?>"/></td>
                      </tr>

                      <tr>
                            <td><label for="active">Active :</label></td>
                            <td>
                                <select name='active' id="active" >
                                    <option value='2' <?php if($active=="2") echo " selected " ?>>yes</option>
                                    <option value='1' <?php if($active=="1") echo " selected " ?>>no</option>
                                </select>
                            </td>
                        </tr>
                      
                    </table>
                    <div class="submit">
                     <input type="hidden" name="formsubmitted" value="TRUE" />
                      <button id="registerBtn" type="submit"> Edit Action </button>
                    </div>

                </div>
          </form>

      </div>
    </body>
    
    
</html>