<?php

namespace Matyotools\TimesheetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DominoCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('harvest:domino')
			->setDescription('Domino timesheet')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$container = $this->getContainer();

		/** @var \Matyotools\DominoBundle\Services\DominoService $domino */
		$domino = $container->get('domino');
		$domino->login();
        $domino->fill();
	}
}
