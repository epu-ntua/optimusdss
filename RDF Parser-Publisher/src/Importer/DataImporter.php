<?php

namespace Sot\optimus\Importer;

//use Sot\optimus\RDF\Publisher;
use Sot\optimus\RDF\RDFGenerator;
use Sot\optimus\tools\FtpLinker;

/**
 * This class implements the data importer. Parses a data source and produces the triples
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class DataImporter
{
    public $headers;    //The sensors mapping array
    public $city;       //set up by the cronjob
    public $site_name;
    public $target;
    public $stream;         //set up by the cronjob
    public $RDFgenerator;
    public $listFiles = array();
    public $conn_id;
    public $creds = array();
    //public $filename;

    public function __construct(RDFgenerator $RDFgenerator)
    {
        $this->RDFgenerator = $RDFgenerator;
        $this->headers = array();

    }

    public function prepareLink()
    {
        $FtpLinker = new FtpLinker($this->creds);
        $this->conn_id = $FtpLinker->conn_id;
        $this->listFiles = $FtpLinker->getFTPList($this->creds);
        if ($this->listFiles ===false) {
            echo "LATHOS";
            die;
        }
    }

    public function doExecute()
    {
        $local_file = "/tmp/ftp_file";

        foreach ($this->listFiles as $filename) {
            ftp_get($this->conn_id, $local_file, $filename, FTP_BINARY);
            $this->parseFile($local_file);
            die;
        }
    }

    /**
    *   This function parses the input file
    */
    public function parseFile($filename)
    {
        echo "\nfilename = ".$filename."\n";

        $fh = fopen($filename, 'r');
        $line_counter = 0;
        $data = array();
        $bulkCounter = 0;
        while (($line = fgetcsv($fh, 1000, ";")) !== false) {
            if ($line_counter++<=0) {
                continue;
            } else {
                $timestamp = str_replace("/", "-", $line[0]." ".$line[1]);

                for ($i=2; $i<sizeof($line); $i++) {
                    $bulkCounter++;
                    $sensor = $this->headers[$i-2];
                    $value = $line[$i];
                    $id = date("YmdHis", strtotime($timestamp));


                    //Generation of the triples
                    $triples = $this->RDFgenerator->generate($id, $this->city, $sensor, $timestamp, $value);
                    $data[] = array('timestamp'=>$timestamp,
                                    'triple'=>$triples,
                                    'stream'=>$this->stream,
                                    'city'=>$this->city,
                                    'site_name'=> $this->site_name,
                                    'sensor'=>$sensor,
                                    'target' => $this->target
                                    );
                    if($bulkCounter%100==0){
                        echo "\nImport Triples\n";
                        \Triple::insert($data);
                        $data = array();
                        $bulkCounter = 0;
                    }
                    //$this->RDFgenerator->saveToDatabase($timestamp, $this->city, $sensor, $this->stream, $triples);
                }

            }
        }
        if (count($data)>0) {
            echo "\nImport Triples Flush\n".count($data)."\n";
            \Triple::insert($data);
        }
    }
}
