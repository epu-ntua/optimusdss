<?php

namespace Sot\optimus\tools;

/**
 * This class implements the data importer. Parses a data source and produces the triples
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class SQLiteLinker
{
    //public $url;
    public $link;

    public function __construct($aFileName){
        try{
            if(is_null($this->link)){
                $sqlite = "sqlite:".$aFileName;
                $this->link = new \PDO($sqlite, "", "",array(\PDO::ATTR_PERSISTENT => true));
                //$this->link =new PDO("sqlite:stat.20151113.135330.db","","",array(
               //                                                                 PDO::ATTR_PERSISTENT => true
                //                                                            ));
            }
            //return $this->pdo;
        }catch(PDOException $e){
            logerror($e->getMessage(), "opendatabase");
            print "Error in openhrsedb ".$e->getMessage();
        }
    }


}
