<?php

namespace Sot\optimus\Importer;

//use Sot\optimus\RDF\Publisher;
use Sot\optimus\RDF\RDFGenerator;
use Sot\optimus\tools\DBLinker;

/**
 * This class implements the data importer. Parses a data source and produces the triples
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class TridiumImporter
{
    public $headers;    //The sensors mapping array
    public $city;       //set up by the cronjob
    public $site_name;       //set up by the cronjob
    public $stream;         //set up by the cronjob
    public $target;
    public $RDFgenerator;
    public $tableList;
    public $readList;
    public $creds = array();
    public $host;
    public $db_name;
    public $link;
    public $module_id;

    public function __construct(RDFgenerator $RDFgenerator)
    {
        $this->RDFgenerator = $RDFgenerator;
        $this->tableList = array();
    }

    //preprocessor last read
    //module id , table name, last id

    public function prepareLink()
    {
        $creds = array(
            'host' => $this->host,
            'username' => $this->creds['username'],
            'password' => $this->creds['password'],
            'db_name' => $this->db_name,
        );
        $DBLinker = new DBLinker($creds);
        $this->link = $DBLinker->link;

    }

    public function checkTable($table_name)
    {
        $query = "SELECT 1 FROM $table_name LIMIT 1";
        $val = mysqli_query($this->link, $query);

        if($val !== false) {
          return true;
        }
        return false;
    }

    public function getDBTableList()
    {
        $query = "SELECT * FROM {$this->db_name}.history_config";
        $result = mysqli_query($this->link, $query);
        while ($row = mysqli_fetch_array($result)) {

            if (stripos($row['TABLE_NAME'], "LOGHISTORY")!==false || stripos($row['TABLE_NAME'], "AUDIT")!==false) {
                continue;
            }
            if($this->checkTable(strtolower($this->db_name.".".$row['TABLE_NAME']))) {
                $this->tableList[] = strtolower($row['TABLE_NAME']);
            }
        }
    }

    public function checkValidValue($aValue)
    {
        $arrayOfValues = array(16,1,2,4,6,20,64);
        /*16    { stale }
        1   {disabled}
        2   {fault}
        4   {down}
        6   {fault,down}
        20  {down,stale}
        64  {null}
        (NULL)  (NULL)*/

        if (is_null($aValue) || in_array($aValue, $arrayOfValues)) {
            return false;
        }
        return true;
    }

    public function parseDBTable($fulltablename)
    {
        //$this->readList = $this->getReadList($this->ModuleInfo['module_id'], $fulltablename);

        $lastId = isset($this->readList[$fulltablename]) ? $this->readList[$fulltablename] : 0;
        $query = "SELECT id,timestamp,value,status FROM {$this->db_name}.$fulltablename WHERE id > $lastId LIMIT 1000";
        $result = mysqli_query($this->link, $query);
        $bulkCounter = 0;
        $data = array();

        while ($row = mysqli_fetch_array($result)) {
            $lastId = $row['id'];
            $bulkCounter++;
            if ($this->checkValidValue($row['status'])) {

                $timestampL = date("Y-m-d H:i", $row['timestamp']/1000);

                //Mapping

                $sensor = isset($this->headers[$fulltablename]['sensor'])?$this->headers[$fulltablename]['sensor']:'sensor';
                $stream = isset($this->headers[$fulltablename]['stream'])?$this->headers[$fulltablename]['stream']:'stream';

                date_default_timezone_set('UTC');
                $id2 = date("YmdHis", strtotime($timestampL));
                $triples = $this->RDFgenerator->generate($id2, $this->city, $sensor, $timestampL, $row['value']);
                //$this->RDFgenerator->saveToDatabase($timestampL, $this->city, $sensor, $stream, $triples);

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

        if (count($data) > 0) {
            echo "\nImport Triples Flush\n".count($data)."\n";
            \Triple::insert($data);
        }

        $dataRead[] = array(
            'module_id' => $this->module_id,
            'name' => $fulltablename,
            'value' => $lastId
        );

        if (isset($this->readList[$fulltablename]) && $this->readList[$fulltablename]>0) {
            \Lastread::where('module_id', $this->module_id)
            ->where('name', $fulltablename)
            ->update(
                [
                    'value' => $lastId,
                ]);
        } else {
            \Lastread::insert($dataRead);
        }

    }

    public function doExecute()
    {
        $this->getDBTableList();
        foreach ($this->tableList as $fulltablename) {
            $this->parseDBTable($fulltablename);
        }
    }
}
