<?php

namespace Sot\optimus\tools;

/**
 * This class implements the data importer. Parses a data source and produces the triples
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class FtpLinker
{
    public $conn_id;
    public $ftp_passive = true;

    public function __construct($aCreds)
    {
        $this->conn_id  = ftp_connect($aCreds['host'],(int)$aCreds['port']) or die("Couldn't connect to server");
    }

    public function getFTPList($aCreds, $search_expr = "*")
    {
        if (@ftp_login($this->conn_id, $aCreds['username'], $aCreds['password']))
        {
            ftp_pasv($this->conn_id, $this->ftp_passive);
            return ftp_nlist($this->conn_id, "-t ".$aCreds['ftp_folder']."/".$search_expr);
        }
        else
        {
            return false;
        }
    }


    public function closeConnection()
    {
        ftp_close($this->conn_id);
    }
}
