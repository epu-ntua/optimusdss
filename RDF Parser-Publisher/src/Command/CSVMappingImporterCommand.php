<?php

namespace Sot\optimus\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sot\optimus\Importer\MappingImporter;

/**
 * Symphony2 CLI Command
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class CSVMappingImporterCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('CSVMapping')
            ->setDescription('Runs the CSV Mapping Importer')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $app;
        $DataImporter = $app->make('\Sot\\optimus\\Importer\\MappingImporter');
        $output->writeln('Running Mapping Importer');
        $data_importer = $DataImporter;

        //  Need to define
        //  C:\DSS\senseone\optimus\src\datafiles\ZandstadStreams.csv
        $data_importer->filename = 'C:\DSS\senseone\optimus\src\datafiles\SavonaSchoolMapping.csv';
        //ZaastadBMS_new.csv
        //$data_importer->filename = '/opt/local/apache2/htdocs/optimus/src/datafiles/Zaastad_flexwhere_points_04-01-2016.csv';
        //$data_importer->filename = '/opt/local/apache2/htdocs/optimus/src/datafiles/SantCugatTheaterBMS20151211.csv';
        $data_importer->posArrays = array(
                'measure' => 7,
                'stream' => 5,
                'sensor' => 6,
                'units' => 3
            );
        $data_importer->module_id = 8;
        $data_importer->site_id = 3;
        $data_importer->doExecute();

    }
}
