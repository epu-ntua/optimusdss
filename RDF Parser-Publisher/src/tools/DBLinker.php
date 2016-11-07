<?php

namespace Sot\optimus\tools;

/**
 * This class implements the data importer. Parses a data source and produces the triples
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class DBLinker
{
    //public $url;
    public $link;

    public function __construct($aCreds)
    {
        $aTmpLink = @mysqli_connect($aCreds['host'], $aCreds['username'], $aCreds['password'], $aCreds['db_name']);
        if (mysqli_connect_error())
        {
            $internalerr = "[".mysqli_connect_errno()."] " . mysqli_connect_error();
            return;
        }
        @mysqli_query($aTmpLink, "set sql_mode = ''");  //Set SQL mode to traditional
        $this->link = $aTmpLink;
    }



    public function closeConnection()
    {
        @mysqli_close($this->link);
    }
}
