OPTIMUS TRACKER

This directory contains the code and the the configuration files of OPTIMUS Tracker.

OPTIMUS Tracker constitutes a web tool for the energy managers, in order to assess the potential of 
the city / building for optimization and identify specific buildings where the OPTIMUS DSS can be applied.


CONFIGURATION
Database

Edit the file includes/config.php with real data, for example:

<?php

  defined('DB_SERVER')? null : define('DB_SERVER', "mydbserver");
  defined('DB_USER')? null : define('DB_USER', "mydbuser");
  defined('DB_NAME')? null : define('DB_NAME', "mydbname");
  defined('DB_PASS')? null : define('DB_PASS', "mydbpass");
  
?>


INSTALLATION
Clone the directory's code and use the db_tracker.sql to import the database schema you need to use the application.
