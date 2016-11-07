<?php

namespace Sot\optimus\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Sot\optimus\Importer\TridiumImporter;

/**
 * Symphony2 CLI Command
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/


class TridiumImporterCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('TRIDIUM')
            ->setDescription('Runs the TRIDIUM Importer')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Running Tridium Importer');
        global $app;
        $modules = new \Module;
        $modulesTridium = $modules::ofType('TRIDIUM')->get();

        $TridiumImporter = $app->make('\Sot\\optimus\\Importer\\TridiumImporter');

        foreach ($modulesTridium as $model) {
            //$city = $model->site->getAttribute('city');

            if($model->isactive == 1)
            {
                $mappingopsTmp = $model->mappingops;
                $headers = array();
                foreach ($mappingopsTmp as $key)
                {
                    $headers[$key->getAttribute('measure')] = array(
                        'stream'=>$key->getAttribute('stream'),
                        'sensor'=>$key->getAttribute('sensor'),
                        'units'=>$key->getAttribute('units')
                    );
                }
                $data_importer = $TridiumImporter;
                //prepare Link for Tridium
                $data_importer->creds = array('username'=>$model->username,'password'=>$model->password);
                $tmpSource = explode(":",$model->source);
                $data_importer->host = $tmpSource[0];
                $data_importer->db_name = $tmpSource[1];
                $data_importer->module_id = $model->id;

                foreach ($model->lastreads as $key)
                {
                    $data_importer->readList[$key->getAttribute('name')] = $key->getAttribute('value');
                }
                $data_importer->target = $model->target;
                $data_importer->prepareLink();
                $data_importer->headers = $headers;
                $data_importer->city = $model->site->city;
                $data_importer->site_name = $model->site->site_name;

                $data_importer->doExecute();
            }
        }
    }
}
