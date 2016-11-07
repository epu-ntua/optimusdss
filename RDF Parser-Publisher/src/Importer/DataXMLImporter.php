<?php

namespace Sot\optimus\Importer;

use Sot\optimus\tools\HttpRequester;
//use Sot\optimus\RDF\Publisher;
use Sot\optimus\RDF\RDFGenerator;

/**
 * This class implements the data importer. Parses a data source and produces the triples
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class DataXMLImporter
{
    public $headers;    //The sensors mapping array
    public $city;       //set up by the cronjob
    public $site_name;       //set up by the cronjob
    public $target;
    public $url;
    public $creds = array();
    public $HttpRequester;
    public $RDFgenerator;
    public $beginDate;
    public $endDate;

    public function __construct(HttpRequester $HttpRequester, RDFgenerator $RDFgenerator)
    {
        $this->Requester = $HttpRequester;
        $this->RDFgenerator = $RDFgenerator;
    }
    public function prepareUrl(){
        $dates = $this->getDates($this->beginDate, $this->endDate);
        $measures = array_keys($this->headers);
        $post = array(  'var'=>$measures,
                        'begin' => $dates['begin'],
                        'end' => $dates['end'],
                        'period' =>'3600'
                        );
        $postString = http_build_query($post, '', '?');
        $postString = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $postString);
        $this->url.= $postString;
    }

    public function getDates($aBegin = null, $aEnd = null)
    {
        if ($aBegin==null || $aEnd==null) {
            date_default_timezone_set('UTC');
            $now = date('Y-m-d');
            //'begin' => '03112014000000',
            //'end' => '03112014235959',

            $before3Days = date('dmY', strtotime($now." -3 day"));
            $before3Days.= '000000';

            $today = date('dmY', strtotime($now));
            $today .= "000000";
            //$next = $yesterday."235959";

            //edw vazei mono gia mia mera prin apo 3 meres apo twra
            //$yesterday .= "000000";
            return array('begin'=>$before3Days,'end'=>$today);
            //return array('begin'=>$yesterday,'end'=>$next);
        } else {
            $testBegin = $this->getDateFromString($aBegin);
            $testEnd = $this->getDateFromString($aEnd);
            if ($testBegin!==false && $testEnd!==false) {
                return array('begin'=>$aBegin,'end'=>$aEnd);
            } else {
                die;
            }
        }


    }

    public function doExecute()
    {
        $xmlFile = $this->Requester->curlPost($this->url, $this->creds);
        $this->Requester->closeConnection();
        ///echo $xmlFile;
        $this->parseFile($xmlFile);
        //die;
    }

    public function getDateFromString($aString)
    {
        //21-10-2014 22:05:00
        $format = '@^(?P<day>\d{2})(?P<month>\d{2})(?P<year>\d{4})(?P<hour>\d{2})(?P<minute>\d{2})(?P<second>\d{2})$@';
        if (preg_match($format, $aString, $dateInfo)==1) {
            return $dateInfo['day']."-".$dateInfo['month']."-".$dateInfo['year']." "
                .$dateInfo['hour'].":".$dateInfo['minute'].":".$dateInfo['second'];
        } else {
            return false;
        }
    }

    public function parseFile($xml_file)
    {
        $xmlStrLog  = simplexml_load_string($xml_file);
        $xml_object = json_decode(json_encode((array)$xmlStrLog), 1);
        $data = array();

        $bulkCounter = 0;

        foreach ($xml_object['record'] as $key => $row) {
            $timestamp =' ';
            $bulkCounter++;
            if (isset($row['dateTime'])) {
                $timestamp = $row['dateTime'];
            }
            if (isset($row['field'])) {
                $tmpArray = $row['field'];
                $device = '';
                $Raw = '';
                foreach ($tmpArray as $key2 => $value) {
                    if (isset($value['id'])) {
                        $device = $value['id'];
                    }
                    if (isset($value['value'])) {
                        $Raw = $value['value'];
                    }

                    $sensor = isset($this->headers[$device]['sensor'])?$this->headers[$device]['sensor']:'sensor';
                    $stream = isset($this->headers[$device]['stream'])?$this->headers[$device]['stream']:'stream';
                    $valueL = $Raw;
                    $timestampL = $this->getDateFromString($timestamp);
                    if ($timestampL !== false && $stream != 'stream') {
                        date_default_timezone_set('UTC');
                        $id2 = date("YmdHis", strtotime($timestampL));
                        $triples = $this->RDFgenerator->generate($id2, $this->city, $sensor, $timestampL, $valueL);
                        //$this->RDFgenerator->saveToDatabase($timestampL, $this->city, $sensor, $stream, $triples);

                        $data[] = array('timestamp'=>$timestampL,
                                    'triple'=>$triples,
                                    'stream'=>$stream,
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

                    }
                }
            }
        }
        if (count($data)>0) {
            echo "\nImport Triples Flush\n".count($data)."\n";
            \Triple::insert($data);
        }
    }
}
