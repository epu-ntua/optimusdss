<?php

class MySQLDatabase{
    public $connection;
    function __construct() {
        $this->open_connection();
    }
    public function open_connection(){
        $this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS);
        if(!$this->connection){
//            die("Database connection failed:".mysql_error()); 
            die("Database connection failed:"); 
        }
        else {
            $db_select = mysql_select_db(DB_NAME,$this->connection );
            if(!$db_select){
                die("Database Selection failed:".mysql_error());
            }
        }
    }
}

$database = new MySQLDatabase();

?>
