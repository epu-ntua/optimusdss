<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        
        
        <title></title>
        <link rel="stylesheet" type="text/css" href="css/general.css">
        
        <script src="scripts/navigationScript.js"> </script>
    </head>
    <?php
        
    ?>
    <body>
     <div class="container" id="workspace">
       <div class="container" id="header">
           <img src="images/sceaf_banner.jpg" id="banner" alt="Optimus Banner">
       </div>
       <div class="container" id="content">
           <h1>
               Welcome to Optimus City Progress Tracker
           </h1>
          
        <?php
          
          if (isset($_POST['formsubmitted'])) {
    
                require_once("includes/config.php");
                require_once("includes/database.php");

                
                $dbc = $database->connection;

                $error = array(); //Declare An Array to store any error message
                if (empty($_POST['name'])) { //if no name has been supplied
                    $error[] = 'Please Enter a name '; //add to array "error"
                } else {
                    $name = $_POST['name']; //else assign it a variable
                }

                if (empty($_POST['Password'])) {
                    $error[] = 'Please Enter Your Password ';
                } else {
                    $Password = $_POST['Password'];
                }

                if (empty($error)) //send to Database if there's no error '

                { // If everything's OK...

                    // Make sure the email address is available:
                    $query = "SELECT * FROM user  WHERE username ='$name' AND password = '$Password' AND active = '1'";
                    $result = mysql_query($query, $dbc);
                    if (!$result) { 
                        echo ' Database Error Occured ';
                    }

                    if (mysql_num_rows($result) == 1) { // IF all OK
                        if (session_status() == PHP_SESSION_NONE) {
                            session_start();
                        }
                        $row = mysql_fetch_array($result);
                        $_SESSION['user'] = $row['id'];
                        $_SESSION['username'] = $row['username'];
                        $type = $row['type'];
                        if($type=="user")
                            header('Location: intro.php');
                        else
                            header('Location: ./admin/actionplans.php');
                    } else { // Wrong user or pass
                        echo '<div class="errormsgbox" >Username and password do not match or your account is inactive!</div>';
                    }

                } else { //If the "error" array contains error msg , display them

                    echo '<div class="errormsgbox"> <ol>';
                    foreach ($error as $key => $values) {

                        echo '	<li>' . $values . '</li>';

                    }
                    echo '</ol></div>';

                }

                mysql_close($dbc); //Close the DB Connection

            }

        ?>
          
          <form action="index.php" method="post" class="registration_form">
            <fieldset>
              <legend>Log In </legend>
               <p>
                  <span style="background: #EAEAEA none repeat scroll 0 0; line-height: 1; padding: 5px 7px;">
                      New member? <a href="registration.php">Register</a>
                  </span> 
              </p>
              
              <div id="registrationInputs">
                <div class="elements">
                  <label for="name">Username :</label>
                  <input type="text" id="name" name="name" size="25" />
                </div>
                <div class="elements">
                  <label for="Password">Password:</label>
                  <input type="password" id="Password" name="Password" size="25" />
                </div>
                <div class="submit">
                  <input type="hidden" name="formsubmitted" value="TRUE" />
                  <button id="loginBtn" type="submit"> Log In </button>
                </div>
              </div>
            </fieldset>
          </form>

        
      </div>
       
	   
       <div class="container" id="footer">
           <p> Copyright © 2016 EPU NTUA</p>
       </div>
     </div>
    </body>
</html>
