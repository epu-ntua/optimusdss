<?php

namespace Sot\optimus\tools;

/**
 * This class implements the data importer. Parses a data source and produces the triples
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class HttpRequester
{
    //public $url;
    public $chandler;

    public function __construct()
    {
        $this->chandler = curl_init();
    }


    public function curlPost($aUrl, $aCreds)
    {
        curl_setopt($this->chandler, CURLOPT_URL, $aUrl);
        curl_setopt($this->chandler, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($this->chandler, CURLOPT_USERPWD, "{$aCreds['username']}:{$aCreds['password']}");
        curl_setopt($this->chandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->chandler, CURLOPT_CONNECTTIMEOUT, 280);
        curl_setopt($this->chandler, CURLOPT_TIMEOUT, 280);
        $ret = curl_exec($this->chandler);
        return (curl_errno($this->chandler) > 0)?false:$ret;
    }

    public function closeConnection()
    {
        curl_close($this->chandler);
    }
}
