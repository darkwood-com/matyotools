<?php

namespace Matyotools\TimesheetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DominoCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('harvest:domino')
			->setDescription('Domino timesheet')
			->addOption('week', null, InputArgument::OPTIONAL, 'Semaine relative')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$container = $this->getContainer();

		/** @var \Matyotools\DominoBundle\Services\DominoWebService $dominoWeb */
		/*$dominoWeb = $container->get('domino_web');
		$dominoWeb->login();
        $dominoWeb->fill();*/

		$date = null;
		$week = $input->getOption('week');
		if($week) {
			$date = new \DateTime($week.' weeks ago');
		}

		/** @var \Matyotools\DominoBundle\Services\DominoDriveService $dominoDrive */
		$dominoDrive = $container->get('domino_drive');
		$timesheet = $dominoDrive->drive($date);

		$rows = array_map(function($line) {
			return array(
				$line['project']['name'],
				$line['project']['dossier'],
				$line['monday'],
				$line['tuesday'],
				$line['wednesday'],
				$line['thursday'],
				$line['friday'],
				$line['saturday'],
				$line['sunday'],
			);
		}, $timesheet);

		$total = array();
		foreach($rows as $row) {
			for($i = 2; $i < 9; $i++) {
				$total[$i] = (isset($total[$i]) ? $total[$i] : 0) + $row[$i];
			}
		}
		$total = array_map(function($num) { return number_format($num, 2); }, $total);

		//total row
		$rows[] = array(
			'', 'total',
			$total[2], $total[3], $total[4], $total[5], $total[6], $total[7], $total[8],
		);

		$week = $dominoDrive->getWeek($date);

		$table = $this->getHelperSet()->get('table');
		$table
			->setHeaders(array(
				'Semaine du ' . $week['monday']->format('d-m-Y') . ' au ' . $week['saturday']->format('d-m-Y'),
				'dossier',
				'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'))
			->setRows($rows)
		;
		$table->render($output);

		$output->writeln('URL : https://dominoweb.domino-info.fr:7001/cgiphl/pw_main.pgm');
		$output->writeln('dossier RTT : 140338');
	}
}
