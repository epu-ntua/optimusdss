<?php

namespace Sot\optimus\Importer;

/**
 * This class implements the Mapping importer.
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class MappingImporter
{
    public $site_id;    //Need to define
    public $module_id;  //Need to define
    public $delimiter = ',';
    // default file Mapping
    // Need to define
    public $filename = "/opt/local/apache2/htdocs/optimus/src/datafiles/ZandstadStreams.csv";
    // Need to define
    // array which holds the indexes of "sensor,stream,measure"
    public $posArrays = array(  'measure' => '',
                                'stream' => '',
                                'sensor' => '',
                                'units' => ''
                                );
    public function __construct()
    {
    }


    public function doExecute()
    {
        $this->parseFile($this->filename);
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
        while (($line = fgetcsv($fh, 1000, $this->delimiter)) !== false)
        {
            if ($line_counter++ <= 0)
            {
                continue;
            }
            else
            {

                $bulkCounter++;
                $measure = $line[$this->posArrays['measure']];
                $stream = $line[$this->posArrays['stream']];
                $sensor = $line[$this->posArrays['sensor']];
                $unit = $line[$this->posArrays['units']];
                $data[] = array('site_id' => $this->site_id,
                                'module_id' => $this->module_id,
                                'measure' => $measure,
                                'stream' => $stream,
                                'sensor' => $sensor,
                                'units' => $unit
                                );

                if($bulkCounter%100 == 0)
                {
                    echo "\nImport Mapping\n";
                    \MappingOp::insert($data);
                    //print_r($data);
                    $data = array();
                    $bulkCounter = 0;
                }
            }
        }
        if (count($data) > 0)
        {
            echo "\nImport Mapping Flush\n".count($data)."\n";
            \MappingOp::insert($data);
        }
    }
}
