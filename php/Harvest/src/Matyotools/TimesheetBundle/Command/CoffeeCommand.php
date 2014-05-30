<?php

namespace Matyotools\TimesheetBundle\Command;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CoffeeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('harvest:coffee')
            ->setDescription('Harvest do the coffee')
        ;
    }

    protected function runCommand($command, InputInterface $input, OutputInterface $output)
    {
        $cmd = null;

        switch($command) {
            case 'running':
                $cmd = new RunningCommand();
                break;
            case 'stats':
                $cmd = new StatsCommand();
                break;
            case 'stop':
                $cmd = new StopCommand();
                break;
            case 'truncate':
                $cmd = new TruncateCommand();
                break;

            default:
                break;
        }

        if(!is_null($cmd)) {
            $cmd->setContainer($this->getContainer());
            $cmd->run($input, $output);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->runCommand('running', new ArrayInput(array()), $output);
        $this->runCommand('stop', new ArrayInput(array()), $output);
        $this->runCommand('stats', new ArrayInput(array()), $output);
        $this->runCommand('truncate', new ArrayInput(array()), $output);
        $this->runCommand('stats', new ArrayInput(array()), $output);
    }
}
