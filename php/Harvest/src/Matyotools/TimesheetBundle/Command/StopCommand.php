<?php

namespace Matyotools\TimesheetBundle\Command;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('harvest:stop')
            ->setDescription('Stop harvest timesheet')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        /** @var \Matyotools\TimesheetBundle\Services\HarvestService $api */
        $api = $container->get('matyotools_timesheet.harvest');
        $api->stop();

        $output->writeln("Stopped running timers");
    }
}
