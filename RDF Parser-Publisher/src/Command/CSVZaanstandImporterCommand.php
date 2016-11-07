<?php

namespace Sot\optimus\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symphony2 CLI Command
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class CSVZaanstandImporterCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('CSVZaanstand')
            ->setDescription('Runs the CSV Zaanstand Importer')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $app;
        $modules = new \Module;
        $modulesCsv = $modules::ofType('CSVZaanstand')->get();
        $DataImporter = $app->make('\Sot\\optimus\\Importer\\CSVZaanstandImporter');
        $output->writeln('Running CSV Importer');
        foreach ($modulesCsv as $model) {
            $headers = array();
            if($model->isactive == 1)
            {
                $mappingopsTmp = $model->mappingops;
                foreach ($mappingopsTmp as $key)
                {
                    $measure = preg_replace('/\s+/', '', $key->getAttribute('measure'));
                    $headers[$measure] = array(
                        'stream'=>$key->getAttribute('stream'),
                        'sensor'=>$key->getAttribute('sensor'),
                        'units'=>$key->getAttribute('units')
                    );
                }
                $data_importer = $DataImporter;
                $tmpSource = explode(":", $model->source);
                $data_importer->creds = array(
                    'host' => $tmpSource[0],
                    'port' => $tmpSource[1],
                    'ftp_folder' => $tmpSource[2],
                    'username' => $model->username,
                    'password' => $model->password
                );
                $data_importer->target = $model->target;
                $data_importer->module_id = $model->id;
                $data_importer->headers = $headers;
                $data_importer->city = $model->site->city;
                $data_importer->site_name = $model->site->site_name;
                $data_importer->doExecute();
            }
        }
    }
}
