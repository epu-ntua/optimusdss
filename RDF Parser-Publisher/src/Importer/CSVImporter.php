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

class CSVImporter
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
    public $startLine;
    public $fileExtention = '';
    public $searchFile;
    public $delimiter;
    public $fileHeaderFlag = false;

    public function __construct(RDFgenerator $RDFgenerator)
    {
        $this->RDFgenerator = $RDFgenerator;
        //$this->headers = array();
    }

    public function init()
    {
    }

    public function setStartLine($aStartLine)
    {
        $this->startLine = $aStartLine;
    }

    public function setDelimiter($aDelimiter)
    {
        $this->delimiter = $aDelimiter;
    }

    public function setFileExtention($aExtention)
    {
        $this->fileExtention = $aExtention;
    }

    public function setFileSeeking($aFileName)
    {
        $this->searchFile = $aFileName;
    }

    public function existsFileHeaders($aPositionFileHeaders = null){
        if(!is_null($aPositionFileHeaders))
        {
            $this->fileHeaderFlag = $aPositionFileHeaders;
        }
        else
        {
            $this->fileHeaderFlag = false;
        }
    }

    public function touchLink(){
        echo "CREDS\n";
        print_r($this->creds);
        ftp_close($this->conn_id);
        $FtpLinker = new FtpLinker($this->creds);
        $this->conn_id = $FtpLinker->conn_id;
    }

    public function prepareLink()
    {
        $FtpLinker = new FtpLinker($this->creds);
        $this->conn_id = $FtpLinker->conn_id;

        $this->listFiles = $FtpLinker->getFTPList($this->creds, $this->searchFile."*.".$this->fileExtention);
        //$this->listFiles = $FtpLinker->getFTPList($this->creds);

        //echo "mpike edw \n";
        //print_r($this->listFiles);
        //die;
        if ($this->listFiles ===false) {
            echo "No List";
            die;
        }
    }

    /**
    *   This function parses the input file as we have one measure per line
    */
    public function parseFile($filename)
    {
        $fh = fopen($filename, 'r');
        $line_counter = 0;
        $data = array();
        $bulkCounter = 0;


        while (($line = fgets($fh)) !== false) {
            //echo "line counter..".$line_counter."\n";
            if ($line_counter++ < $this->startLine-1 )
            {
                continue;
            }
            else if($this->fileHeaderFlag !== false && $line_counter == $this->fileHeaderFlag)
            {
                echo "mpike edw h malakia\n";
                $lineArray = explode($this->delimiter, $line);
                echo "line counter MESA= ".$line_counter."\n";
                echo "this->fileHeaderFlag = ".$this->fileHeaderFlag."\n";
                $this->parseFileHeaders($lineArray);
            }
            else
            {
                $lineArray = explode($this->delimiter, $line);
                $measure = '';
                if (($result = $this->parseLineArray($lineArray)) !== FALSE )
                {
                    for ($i=0; $i < count($result) ; $i++)
                    {
                        $measure = $result[$i]['measure'];
                        $sensor = 'sensor';
                        $stream = 'stream';
                        $value = $result[$i]['value'];
                        //echo "\nVALUE=".$value."\n";
                        if(!is_numeric($value))
                        {
                            echo "DEn einai numeric\n#########\n\n";
                            continue;
                        }

                        if(isset($this->headers[$measure]))
                        {
                            $sensor = $this->headers[$measure]['sensor'];
                            $stream = $this->headers[$measure]['stream'];
                        }

                        echo "measure = ".$measure."\t"."stream = ".$stream."\t"."value = ".$value."\n";

                        $id = date("YmdHis", strtotime($result[$i]['timestamp']));

                        //Generation of the triples
                        if ($stream != 'stream') {
                            $bulkCounter++;
                            $triples = $this->RDFgenerator->generate(
                                $id,
                                $this->city,
                                $sensor,
                                $result[$i]['timestamp'],
                                $value
                            );

                            $data[] = array(
                                'timestamp' => $result[$i]['timestamp'],
                                'triple' => $triples,
                                'stream' => $stream,
                                'city' => $this->city,
                                'site_name'=> $this->site_name,
                                'sensor' => $sensor,
                                'target' => $this->target
                            );
                            if ($bulkCounter%500 == 0) {
                                echo "\nImport Triples...";
                                \Triple::insert($data);
                                $data = array();
                                $bulkCounter = 0;
                            }
                        }
                    }
                }
            }
        }
        if (count($data)>0) {
            echo "\nImport Triples Flush\n".count($data)."\n";
            \Triple::insert($data);
        }
    }

    public function doExecute()
    {
        $this->init();
        $this->prepareLink();

        //$local_file = "/tmp/".getmypid();
        $local_file = 'C:\DSS\senseone\optimus\src\datafiles'.DIRECTORY_SEPARATOR.getmypid();
        print_r($this->listFiles);
        foreach ($this->listFiles as $filename)
        {
            ftp_get($this->conn_id, $local_file, $filename, FTP_BINARY);
            echo "\nParsing \"$filename\" \n";
            $this->parseFile($local_file);
            //check if ftp is connection is up
            $res = ftp_size($this->conn_id, $filename);
            echo " 1 conn_id".$this->conn_id."\n";
            if ($res == -1) {
                $this->touchLink();
                echo " 2 conn_id".$this->conn_id."\n";
            }
            ftp_rename($this->conn_id, $filename, dirname($filename)."/processed/".basename($filename));

        }
        if(is_file($local_file))
        {
            unlink($local_file);
        }
    }
}
