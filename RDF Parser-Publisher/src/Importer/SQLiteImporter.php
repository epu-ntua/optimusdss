<?php

namespace Sot\optimus\Importer;

//use Sot\optimus\RDF\Publisher;
use Sot\optimus\RDF\RDFGenerator;
use Sot\optimus\tools\SQLiteLinker;
use Sot\optimus\tools\FtpLinker;

/**
 * This class implements the data importer. Parses a data source and produces the triples
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class SQLiteImporter
{
    public $headers;    //The sensors mapping array
    public $city;       //set up by the cronjob
    public $site_name;       //set up by the cronjob
    public $stream;         //set up by the cronjob
    public $target;
    public $RDFgenerator;
    public $readList;
    public $creds = array();
    public $link; //sql lite link
    public $module_id;
    public $searchFile;


    public $conn_id;    //ftp link

    public function __construct(RDFgenerator $RDFgenerator)
    {
        $this->RDFgenerator = $RDFgenerator;
    }

    public function init()
    {
        $this->setFileExtention("db");
        $this->setFileSeeking($this->searchFile);
    }


    public function setFileExtention($aExtention)
    {
        $this->fileExtention = $aExtention;
    }

    public function setFileSeeking($aFileName)
    {
        $this->searchFile = $aFileName;
    }

    //preprocessor last read
    //module id , table name, last id



    public function prepareLink()
    {
        $FtpLinker = new FtpLinker($this->creds);
        $this->conn_id = $FtpLinker->conn_id;

        $this->listFiles = $FtpLinker->getFTPList($this->creds, $this->searchFile."*.".$this->fileExtention);
        //$this->listFiles = $FtpLinker->getFTPList($this->creds);

        echo "mpike edw \n";
        //print_r($this->listFiles);
        //die;
        if ($this->listFiles ===false) {
            echo "No List";
            die;
        }
    }

    public function touchLink(){
        echo "CREDS\n";
        print_r($this->creds);
        ftp_close($this->conn_id);
        $FtpLinker = new FtpLinker($this->creds);
        $this->conn_id = $FtpLinker->conn_id;
    }

    public function processSqlLiteFile($aFileName)
    {
        $this->prepareSqliteLink($aFileName);
        $this->parseSQliteTable();
        unset($this->link);

    }

    public function prepareSqliteLink($aFileName)
    {
        $SQLiteLinker = new SQLiteLinker($aFileName);
        $this->link = $SQLiteLinker->link;
    }

    public function parseSQliteTable() {

        $lastId = isset($this->readList['last_run']) ? $this->readList['last_run'] : 0;
        echo "Lastid = ".$lastId."\n";
        //$sql = 'SELECT * FROM ruimte_bezetting WHERE id > 0 ORDER BY id LIMIT 3';
        //$sql = "SELECT * FROM ruimte_bezetting WHERE id > {$lastId} ORDER BY id LIMIT 3";
        $sql = "SELECT * FROM ruimte_bezetting WHERE id > {$lastId} ORDER BY id";



        //echo "\nsql = ".$sql."\n";
        try {
            $stmt = $this->link->query($sql);
            $bulkCounter = 0;
            $data = array();
            $arrayDates = array();
            while ($row = $stmt->fetch()) {
                $lastId = $row['id'];
                $date = $row['datum'];
                $measure = $row['ruimte_identificatie'];
                //echo "\nruimte_identificatie:\n".$measure."</br>";
                if(!isset($arrayDates[$date])){
                    $arrayDates[$date] = $this->mapQuarters($date);
                    //print_r($arrayDates);
                }
                for ($i=0; $i <=95 ; $i++) {
                    $id = 'k'.$i;
                    $value = $row[$id];
                    if(!is_null($value) && $value != ''){
                        $bulkCounter++;
                        print "$id \t value =". $value ."\t". $arrayDates[$date][$i]."</br>";
                        $timestampL = $arrayDates[$date][$i];
                        //Mapping
                        $sensor = isset($this->headers[$measure]['sensor'])?$this->headers[$measure]['sensor']:'sensor';
                        $stream = isset($this->headers[$measure]['stream'])?$this->headers[$measure]['stream']:'stream';
                        if($stream != 'stream' && $sensor != 'sensor'){
                            date_default_timezone_set('UTC');
                            $id2 = date("YmdHis", strtotime($timestampL));
                            $triples = $this->RDFgenerator->generate($id2, $this->city, $sensor, $timestampL, $value);
                            $data[] = array(
                                'timestamp' => $timestampL,
                                'triple' => $triples,
                                'stream' => $stream,
                                'city' => $this->city,
                                'site_name'=> $this->site_name,
                                'sensor' => $sensor,
                                'target' => $this->target
                            );

                            if ($bulkCounter%100==0) {
                                echo "\nImport Triples\n";
                                \Triple::insert($data);
                                $data = array();
                                $bulkCounter = 0;
                            }
                        }
                    }
                }


            }
            if (count($data) > 0) {
                echo "\nImport Triples Flush\n".count($data)."\n";
                \Triple::insert($data);
            }

            $dataRead[] = array(
                            'module_id' => $this->module_id,
                            'name' => 'last_run',
                            'value' => $lastId
                            );

            if (isset($this->readList['last_run']) && $this->readList['last_run'] > 0) {
                \Lastread::where('module_id', $this->module_id)
                ->where('name', 'last_run')
                ->update(
                    [
                        'value' => $lastId,
                    ]);
            } else {
                \Lastread::insert($dataRead);
            }


        }
        catch (PDOException $e) {
            print $e->getMessage();
        }
    }

    public function mapQuarters($adateTime)
    {

        $minutes_to_add = 15;
        $time = new \DateTime($adateTime." ".'00:00:00');
        $arrayDates = array();
        for($i = 0; $i < 96; $i++){
            $time->add(new \DateInterval('PT' . $minutes_to_add . 'M'));
            $stamp = $time->format('Y-m-d H:i:s');
            $arrayDates[]= $stamp ;
        }
        return $arrayDates;
    }


    public function doExecute()
    {
        $this->init();
        $this->prepareLink();
        //$local_file = "/tmp/".getmypid().".db";
        $local_file = 'C:\DSS\senseone\optimus\src\datafiles'.DIRECTORY_SEPARATOR.getmypid();
        print_r($this->listFiles);
        foreach ($this->listFiles as $filename)
        {
            ftp_get($this->conn_id, $local_file, $filename, FTP_BINARY);
            echo "\nParsing \"$filename\" \n";
            echo "\local filename \"$local_file\" \n";
            $this->processSqlLiteFile($local_file);
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
