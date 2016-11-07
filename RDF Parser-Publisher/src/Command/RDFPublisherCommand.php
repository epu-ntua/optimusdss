<?php

namespace Sot\optimus\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;

use Sot\optimus\RDF\Publisher;

/**
 * Symphony2 CLI Command
 * @package optimus
 * @author SenseOne Technologies
 * @copyright Copyright (c) 2015, SenseOne Technologies
*/

class RDFPublisherCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('publish')
            ->setDescription('Executes the RDF Publisher')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $link;

        $output->writeln('Running RDF Publisher');

        $publisher = new Publisher();
        $publisher->useCurl = false;

        $query = "SELECT * FROM triples ORDER BY id ASC LIMIT 1000";
        $res = mysqli_query($link, $query);

        $arrayDelete = array();
        while ($row = mysqli_fetch_array($res)) {
            $stream = $row['stream'];
            $triples = $row['triple'];
            $target = $row['target'];

            $publisher->basicUri = $target;

            $publisher->publish($publisher->getUid(), $stream, $triples);
            $arrayDelete[] = $row['id'];

            if (sizeof($arrayDelete) >= 500) {
                $query = "DELETE FROM triples WHERE id IN (".implode(",", $arrayDelete).")";
                mysqli_query($link, $query);
                $arrayDelete = array();
            }
        }

        if (count($arrayDelete) > 0) {
            $query = "DELETE FROM triples WHERE id IN (".implode(",", $arrayDelete).")";
            mysqli_query($link, $query);
        }
    }
}
