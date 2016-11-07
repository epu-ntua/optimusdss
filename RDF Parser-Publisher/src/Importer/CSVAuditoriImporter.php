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

class CSVAuditoriImporter extends CSVImporter
{
    public function init()
    {
        $this->setStartLine(3);
        $this->setFileExtention("txt");
        $this->setDelimiter("\t");
        $this->setFileSeeking($this->searchFile);
    }

    public function parseLineArray($lineArray)
    {
        //print_r($lineArray);
        $returnedArray = array();
        if (!isset($lineArray[6])) {
            return false;
        }

        //$sensor = mb_convert_encoding($lineArray[1], "UTF-8", "UTF-16");
        //(15 minuto) is excluded from data files
        $measureArray = explode("(", $lineArray[4]);
        $measure = mb_convert_encoding($measureArray[0], "UTF-8", "UTF-16");
        $measure = preg_replace('/\s+/', '', $measure);
        $timestamp = mb_convert_encoding($lineArray[6], "UTF-8", "UTF-16");
        $value = mb_convert_encoding($lineArray[7], "UTF-8", "UTF-16");
        $value = str_replace(",", ".", $value);


        $returnedArray[]= array(
            "measure" => $measure,
            //"sensor" => $sensor,
            "timestamp" => $timestamp,
            "value" => (float)$value
        );
        return $returnedArray;
    }
}
