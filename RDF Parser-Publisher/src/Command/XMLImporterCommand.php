<?php

namespace Sot\optimus\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;

use Sot\optimus\Importer\DataXMLImporter;

/**
 * Symphony2 CLI Command
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class XMLImporterCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('XML')
            ->setDescription('Runs the XML Importer')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('begin', 'b', InputOption::VALUE_OPTIONAL),
                    new InputOption('end', 'e', InputOption::VALUE_OPTIONAL),
                ))
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //global $link;

        $output->writeln('Running XML Importer');
        global $app;
        $modules = new \Module;
        $modulesXml = $modules::ofType('XML')->get();
        $DataXMLImporter = $app->make('\Sot\\optimus\\Importer\\DataXMLImporter');

        $begin = $input->getOption('begin')?$input->getOption('begin'):null;
        $end = $input->getOption('end')?$input->getOption('end'):null;

        foreach ($modulesXml as $model) {
            //$city = $model->site->getAttribute('city');

            if($model->isactive == 1)
            {
                $mappingopsTmp = $model->mappingops;
                $headers = array();
                foreach ($mappingopsTmp as $key) {
                    $headers[$key->getAttribute('measure')] = array(
                                                            'stream'=>$key->getAttribute('stream'),
                                                            'sensor'=>$key->getAttribute('sensor'),
                                                            'units'=>$key->getAttribute('units')
                                                            );
                }
                $data_importer = $DataXMLImporter;
                $data_importer->target = $model->target;
                $data_importer->creds = array('username'=>$model->username,'password'=>$model->password);
                $data_importer->beginDate = $begin;
                $data_importer->endDate = $end;
                $data_importer->url = $model->source;
                $data_importer->headers = $headers;
                $data_importer->city = $model->site->city;
                $data_importer->site_name = $model->site->site_name;
                $data_importer->prepareUrl();
                //print_r($headers);
                $data_importer->doExecute();
            }
        }

    }
}
