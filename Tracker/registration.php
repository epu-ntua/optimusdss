<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="css/NavBar.css"> 
        <link rel="stylesheet" type="text/css" href="css/index.css">
        <link rel="stylesheet" type="text/css" href="css/buttons.css">
        <link rel="stylesheet" type="text/css" href="css/registration.css"> 
        <title>Welcome</title>
    </head>
    <body>
      <div id="workspace">
        
        <h1 id='welcomeIndex'>Welcome</h1>
        <?php
          
          if (isset($_POST['formsubmitted'])) {
    
                require_once("includes/config.php");
                require_once("includes/database.php");

                define('EMAIL', 'registration@sceaf.com');
                DEFINE('WEBSITE_URL', 'http://localhost');
                $dbc = $database->connection;

                $error = array(); //Declare An Array to store any error message
                if (empty($_POST['name'])) { //if no name has been supplied
                    $error[] = 'Please Enter a name '; //add to array "error"
                } else {
                    $name = $_POST['name']; //else assign it a variable
                }

                if (empty($_POST['e-mail'])) {
                    $error[] = 'Please Enter your Email ';
                } else {

                    if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",
                        $_POST['e-mail'])) {
                        //regular expression for email validation
                        $Email = $_POST['e-mail'];
                    } else {
                        $error[] = 'Your EMail Address is invalid  ';
                    }

                }

                if (empty($_POST['Password'])) {
                    $error[] = 'Please Enter Your Password ';
                } else {
                    $Password = $_POST['Password'];
                }
                
                if (empty($_POST['firstname'])) {
                    $error[] = 'Please Enter Your First Name ';
                } else {
                    $firstname = $_POST['firstname'];
                }
                
                if (empty($_POST['lastname'])) {
                    $error[] = 'Please Enter Your Last Name ';
                } else {
                    $lastname = $_POST['lastname'];
                }

                if (empty($error)) //send to Database if there's no error '

                { // If everything's OK...

                    // Make sure the email address is available:
                    $query_verify_email = "SELECT * FROM user  WHERE email ='$Email'";
                    $result_verify_email = mysql_query($query_verify_email, $dbc);
                    if (!$result_verify_email) { //if the Query Failed ,similar to if($result_verify_email==false)
                        echo ' Database Error Occured ';
                    }

                    if (mysql_num_rows($result_verify_email) == 0) { // IF no previous user is using this email .

                        $query_insert_user =
                            "INSERT INTO user ( username, email, password, active, firstname, lastname, type) VALUES ( '$name', '$Email', '$Password', '1', '$firstname', '$lastname', 'user')";

                        $result_insert_user = mysql_query( $query_insert_user, $dbc);
                        if (!$result_insert_user) {
                            echo 'Query Failed ';
                        }

                        if (mysql_affected_rows($dbc) == 1) { //If the Insert Query was successfull.
                            echo '<div class="success">Thank you for registering! <a href="index.php" > Click to login! </a>  </div>';

                        } else { // If it did not run OK.
                            echo '<div class="errormsgbox">You could not be registered due to a system error. We apologize for any inconvenience.</div>';
                        }

                    } else { // The email address is not available.
                        echo '<div class="errormsgbox" >That email address has already been registered. </div>';
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
          
        
          <form action="registration.php" method="post" class="registration_form">
            <fieldset>
              <legend>Registration Form </legend>

              <p>
                  <span style="background:#EAEAEA none repeat scroll 0 0;line-height:1;margin-left:400px;padding:5px 7px;">
                    Already a member? <a href="index.php">Log in</a>
                  </span> 
              </p>
              <div id="registrationInputs">
                <div class="elements">
                  <label for="e-mail">E-mail :</label>
                  <input type="text" id="e-mail" name="e-mail" size="25" />
                </div>
                <div class="elements">
                  <label for="firstname">First Name :</label>
                  <input type="text" id="firstname" name="firstname" size="25" />
                </div>
                <div class="elements">
                  <label for="lastname">Last Name :</label>
                  <input type="text" id="lastname" name="lastname" size="25" />
                </div>
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
                  <button id="registerBtn" type="submit"> Register </button>
                </div>
              </div>
            </fieldset>
          </form>

        
      </div>
    </body>
</html>