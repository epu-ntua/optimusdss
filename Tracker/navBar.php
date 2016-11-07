<nav class="navbar navbar-default">
  <div class="container-fluid">
     <div>
       <ul class="nav navbar-nav">
            <li <?php if((strpos($_SERVER['REQUEST_URI'],'insert') == false) && (strpos($_SERVER['REQUEST_URI'],'past') == false)){ 
                            echo ' class = "active" ';
                          } 
                ?>>  <a class="menuItem" href="/intro.php">Home</a></li>
<!--            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">New Data Input
                    <span class="caret"></span>
                </a>
                 <ul class="dropdown-menu">
                     <li><a href="/CityTool_v2/insert/doInitializationInsert.php">New City</a></li>
                     <li><a href="/CityTool_v2/insert/selectCity.php">Existing City</a></li>
                </ul>
            </li>-->
            <li <?php if(strpos($_SERVER['REQUEST_URI'],'insert') != false) { 
                            echo ' class = "active" ';
                          } 
                ?>><a class="menuItem" href="/insert/doInitializationInsert.php">New Data Input</a></li> 
            <li <?php if(strpos($_SERVER['REQUEST_URI'],'past') != false){ 
                            echo ' class = "active" ';
                          } 
                          ?>><a class="menuItem" href="/past/submissions.php"">Past Submissions</a></li> 
       </ul>
         <div id="username">
        <a id="user"> User: <?php if (session_status() == PHP_SESSION_NONE) {
                                    session_start();
                                  } 
                                  echo $_SESSION['username'];
                            ?>, 
        </a>
        <a id="logout" href ="<?php if((strpos($_SERVER['REQUEST_URI'],'past') != false)||(strpos($_SERVER['REQUEST_URI'],'insert') != false)||(strpos($_SERVER['REQUEST_URI'],'admin') != false)){ echo '../'; }?>doLogout.php">logout</a>
    </div>
     </div>
  </div>
    
</nav>
