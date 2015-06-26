<?php

namespace Matyotools\DominoBundle\Services;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Post\PostFile;
use Matyotools\TimesheetBundle\Services\HarvestService;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Message\ResponseInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class DominoDriveService
{
	/**
	 * @var string
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var HarvestService
	 */
	protected $harvestService;

	/**
	 * @var array
	 */
	private $daysInWeek;

	/**
	 * @var string
	 */
	private $genDir;

    public function __construct($user, $password, $harvestService)
    {
        $this->user = $user;
        $this->password = $password;
		$this->harvestService = $harvestService;

		$this->daysInWeek = ['monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 7];
		$this->genDir = __DIR__ . '/../../../../domino/tests';
    }

	/**
	 * @param \DateTime $date
	 * @return array
	 */
	public function getWeek($date = null)
	{
		if(is_null($date)) {
			$date = new \DateTime();
		}

		$week = $this->daysInWeek;

		$date = clone $date;
		foreach($week as $key => $day)
		{
			if(is_null($date)) {
				$dayDate = new \DateTime();
			} else {
				$dayDate = clone $date;
			}
			$week[$key] = $dayDate->setISODate($dayDate->format('o'), $dayDate->format('W'), $day);
		}

		return $week;
	}

	public function getTimesheet($date = null)
	{
		if(is_null($date)) {
			$date = new \DateTime();
		}
		$week = $this->getWeek($date);

		$range = $this->harvestService->getRangeDays($week['monday'], $week['sunday']);
		$projects = $this->bindHarvestToDomino();

		$data = array();
		foreach($range as $entry) {
			if(!isset($data[$entry->get('project-id')][$entry->get('spent-at')])) {
				$data[$entry->get('project-id')][$entry->get('spent-at')] = 0;
			}
			$data[$entry->get('project-id')][$entry->get('spent-at')] += floatval($entry->get('hours'));
		}

		$rows = array();
		foreach($data as $projectId => $times) {
			if(!isset($projects[$projectId])) {
				throw new Exception(sprintf('Project id %s not found !', $projectId));
			}

			$row = array(
				'project' => $projects[$projectId],
				'monday' => 0,
				'tuesday' => 0,
				'wednesday' => 0,
				'thursday' => 0,
				'friday' => 0,
				'saturday' => 0,
				'sunday' => 0,
			);

			foreach($week as $day => $date) {
				/** @var \DateTime $date */
				$date = $date->format('Y-m-d');
				if(isset($times[$date])) {
					$row[$day] = $times[$date];
				}
			}

			$rows[] = $row;
		}

		return $rows;
	}

    public function normalizeTimesheet($timesheet)
    {
        $rows = array();

        foreach($timesheet as $row) {
            $sum = 0;
            foreach($this->daysInWeek as $day => $value) {
                $row[$day] = round($row[$day] * 4) / 4;

                $sum += $row[$day];
            }

            if($sum > 0) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

	public function bindHarvestToDomino()
	{
		return array(
			'6938786' => array('name' => 'PRIMONIAL - Partenaires',	     'client' => '', 'dossier' => ''),
			'7376843' => array('name' => 'AO NRJ GAMES',                 'client' => '', 'dossier' => ''),
			'6445332' => array('name' => 'KRONENBOURG - Tourtel Twist',  'client' => '', 'dossier' => ''),
			'6579884' => array('name' => 'GRATTA E VINCI - Lottomatica', 'client' => '', 'dossier' => ''),
			'6664888' => array('name' => 'EVOLUPHARM',                   'client' => '', 'dossier' => ''),
		);
	}

	public function generate($date = null)
	{
		$timesheet = $this->normalizeTimesheet($this->getTimesheet($date));

		/*$finder = new Finder();
		$finder->in($this->genDir)->name('*.gen.js');
		foreach($finder as $file) {
			$dateString = substr($file->getFilename(), 0, 10);
			$date = \DateTime::createFromFormat('Y-m-d', $dateString);
		}*/

		$week = $this->getWeek(new \DateTime());
		$genFile = $this->genDir . '/' . $week['monday']->format('Y-m-d') . '.gen.js';

		$script = <<<SCRIPT
module.exports = {
	'Test domino' : function (browser) {
		var params = browser.globals.test_settings.globals;

        var wait = 5000,
			saisieDesTempsPath = '#menu_3',
			frameSaisieDesTempsPath = "#href_1",
			clientPath = "#f1_28_sel",
			dossierPath = "#f1_30_phl",
			buttonAddPath = "#f1_pw_btndyn_201";

		browser
			.url('https://dominoweb.domino-info.fr:7001/cgiphl/pw_main.pgm')
			.waitForElementVisible('body', wait)
			.setValue('input[name="name1"]', params.user)
			.setValue('input[name="name2"]', params.password)
			.waitForElementVisible('input[type="button"][value="OK"]', wait)
			.click('input[type="button"][value="OK"]')
            .waitForElementVisible(saisieDesTempsPath, wait)
			.click(saisieDesTempsPath)
			.waitForElementPresent(frameSaisieDesTempsPath, wait)
			.getAttribute(frameSaisieDesTempsPath, "src", function(data) {
				console.log(data.value);
				this.url(data.value);
				/*this.waitForElementPresent(clientPath, wait)
					.waitForElementPresent(dossierPath, wait)
					.setValue(clientPath, "AEGE GROUPE/Plan de Communication/Sté 07")
					.setValue(dossierPath, "78282")
					.click(buttonAddPath);*/
			})
			.pause(wait)
			.end();
	}
};
SCRIPT;

		$filesystem = new Filesystem();
		$filesystem->dumpFile($genFile, $script);

		return $timesheet;
	}

	/**
	 * @param |DateTime|null $date
	 * @return array
	 */
	public function drive($date = null)
	{
		$timesheet = $this->generate($date);

		return $timesheet;
	}
}
