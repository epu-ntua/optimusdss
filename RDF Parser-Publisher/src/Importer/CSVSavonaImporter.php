<?php

namespace Sot\optimus\Importer;

//use Sot\optimus\RDF\Publisher;
//use Sot\optimus\RDF\RDFGenerator;
use Sot\optimus\tools\FtpLinker;
use Sot\optimus\Importer\CSVImporter;

/**
 * This class implements the data importer. Parses a data source and produces the triples
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class CSVSavonaImporter extends CSVImporter
{

    public $fileHeaders = array();

    public function init()
    {
        $this->setStartLine(1);
        $this->setFileExtention("csv");
        $this->setDelimiter(";");
        $this->existsFileHeaders(1);
        //echo "searchfile ZZXXXX=".$this->searchFile."\n";
        $this->setFileSeeking($this->searchFile);
    }

    public function parseFileHeaders($lineArray)
    {
        $this->fileHeaders = array_slice($lineArray, 2);
    }

    public function parseLineArray($lineArray)
    {
        if (!isset($lineArray[0]))
        {
            return false;
        }

        $date = str_replace('/','-',$lineArray[0]);
        $time = ' ';
        $time.= $lineArray[1];
        $timestamp = date("Y-m-d H:i:s", strtotime($date.$time));
        $returnedArray = array();

        for($i = 2; $i < sizeof($lineArray); $i++)
        {
            //link data with its file headers
            $measure = preg_replace('/\s+/', '',$this->fileHeaders[$i-2]);
            $value = str_replace(',','.',$lineArray[$i]);
            $value = trim(preg_replace('/\s+/', '', $value));
            $sensor = '';
            $returnedArray[]= array(
                        "measure" => $measure,
                        "sensor" => $sensor,
                        "timestamp" => $timestamp,
                        "value" => $value
            );
        }

        return $returnedArray;

        /*$sensor = $lineArray[9];
        $measure = preg_replace('/\s+/', '', $lineArray[10]);
        $value = $lineArray[8];

        return array(
            "measure" => $measure,
            "sensor" => $sensor,
            "timestamp" => $timestamp,
            "value" => $value
        );*/
    }
}
