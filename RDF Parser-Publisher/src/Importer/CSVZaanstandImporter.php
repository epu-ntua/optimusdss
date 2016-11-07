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

class CSVZaanstandImporter extends CSVImporter
{
    public function init()
    {
        $this->setStartLine(3);
        $this->setFileExtention("csv");
        $this->setDelimiter(",");
    }


    public function parseLineArray($lineArray)
    {
        $returnedArray = array();
        if (!isset($lineArray[6])) {
            return false;
        }

        $sensor = $lineArray[9];
        $measure = preg_replace('/\s+/', '', $lineArray[10]);
        $timestampA = explode('.', $lineArray[1]);
        $timestamp = $timestampA[0];
        $value = $lineArray[8];
        $returnedArray[]= array(
            "measure" => $measure,
            "sensor" => $sensor,
            "timestamp" => $timestamp,
            "value" => $value
        );

        return $returnedArray;

        /*return array(
            "measure" => $measure,
            "sensor" => $sensor,
            "timestamp" => $timestamp,
            "value" => $value
        );*/
    }
}
