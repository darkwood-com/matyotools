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
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Message\ResponseInterface;

class DominoService
{
	/**
	 * @var History
	 */
	protected $history;

	/**
	 * @var GuzzleClient
	 */
	protected $client;

	/**
	 * @var string
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $password;

    /**
     * @var string
     */
    protected $pw_id;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;

		$this->history = new History();

		$guzzle = new GuzzleClient();
        $guzzle->getEmitter()->attach($this->history);

        $this->client = new GuzzleClient();
        //$this->client->setClient($guzzle);
    }

	/**
	 * @param Response $response
	 * @return Crawler
	 */
	private function createCrawler($response)
	{
		$crawler = new Crawler();
		$crawler->addContent($response->getBody(), $response->getHeader('Content-Type'));

		return $crawler;
	}

	public function login()
	{
		$url = 'https://dominoweb.domino-info.fr:7001/cgiphl/pw_login.pgm';
		$response = $this->client->post($url, array(
            'headers' => array(
                'Content-type' => 'application/x-www-form-urlencoded;charset=UTF-8',
            ),
			'body' => array(
				'function' => 1,
				'name1' => $this->user,
				'name2' => $this->password,
				'apkode' => '',
			),
		));
        $body = (string) $response->getBody();
        if(preg_match('/pw_id="([^"]+)"/', $body, $m)) {
            $this->pw_id = $m[1];
        }
	}

    public function timesheet()
    {
        $url = 'https://dominoweb.domino-info.fr:7001/cgiphl/PW_CALL.PGM';
        $url = $url . '?' . http_build_query(array(
            'appli' => 'PHLGIF',
            'prop' => '',
            'num' => '646',
            'WINTITLE' => 'Saisie des temps',
            'PW_TABNUM' => '2',
            'id' => $this->pw_id,
        ));
        $response = $this->client->get($url);
        $body = (string) $response->getBody();

		$crawler = $this->createCrawler($response);
		$form = $crawler->filter('form')->text();
    }

    public function fill()
    {
        $url = 'https://dominoweb.domino-info.fr:7001/cgiphl/PW_CGI.PGM';
        $response = $this->client->post($url, array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'body' => array(
                'phl_ini' => '0',
                'pw_lang' => 'FR',
                'pw_id' => $this->pw_id,
                'pw_killjob' => '0',
                'phl_job' => 'WEB2448231',
                'phl_dtaq' => 'WEB2448231',
                'phl_user' => 'MKH_MLEDR',
                'phl_joblimit' => '0',
                'phl_jobnum' => '384549',
                'pw_applinum' => 'PHLGIF',
                'pw_applilib' => 'PHLGIF',
                'pw_version' => '1433094444',
                'pw_posy' => '282',
                'pw_posx' => '218',
                'pw_csry' => '0',
                'pw_csrx' => '0',
                'pw_wcsry' => '0',
                'pw_wcsrx' => '0',
                'pw_csrn' => 'S1MERCREDI',
                'pw_csrf' => 'FMTSFL1',
                'pw_key' => '013000',
                'pw_rollnext' => '',
                'pw_rollprio' => '',
                'pw_sflsort' => '',
                'pw_sflclr' => '',
                'pw_fldsort' => '',
                'pw_ordsort' => '',
                'pw_sflnbl' => '',
                'pw_nblnbl' => '',
                'pw_invite' => '',
                'pw_cssrep' => 'newdesign',
                'pw_winstyle' => 'newtheme',
                'f1_colnum' => '1  2  3  4  5  6  7  8  9  10 11 12 13',
                'f1_colwid' => '29 10212324413214554 60 64 58 73 62 62',
                'f1_colfix' => '0000000000000',
                'f1_20' => '',
                'f1_21' => '',
                'f1_22' => '1',
                'f1_23' => '0',
                'f1_28_chg' => '',
                'f1_28' => '',
                'f1_30_chg' => '',
                'f1_30' => '',
                'f1_32' => '0',
                'f1_35_chg' => '',
                'f1_35' => '24',
                'f1_37_chg' => '',
                'f1_37' => '20',
                'f1_38_chg' => '',
                'f1_38' => '25',
                'f1_40' => '1,00',
                'f1_71_chg' => '',
                'f1_71' => '840',
                'f1_80_chg' => '',
                'f1_80' => '0',
                'f1_1_86' => '',
                'f1_1_87' => '',
                'f1_1_88' => '24',
                'f1_1_89' => '840',
                'f1_1_90' => '8',
                'f1_1_91' => '6',
                'f1_1_92' => '20',
                'f1_1_93' => '15',
                'f1_1_94' => '13',
                'f1_1_95' => '6',
                'f1_1_96' => '20',
                'f1_1_97' => '15',
                'f1_1_98' => '7',
                'f1_1_99' => '1',
                'f1_1_100' => '140378',
                'f1_1_101' => '20',
                'f1_1_102' => '25',
                'f1_1_103' => '4,25',
                'f1_1_111' => '140378-Plateforme Multi-Sites',
                'f1_1_112' => '20-INTÉGRATION ET DÉV',
                'f1_1_113' => '25-DÉVELOPPEUR SR',
                'f1_1_114' => '0',
                'f1_1_115' => '2,00',
                'f1_1_117' => '0,50',
                'f1_1_118' => '1.50',
                'f1_1_119' => '0,75',
                'f1_1_120' => '2,50',
                'f1_1_121' => '0,00',
                'sfl1_lineh' => '22',
                'sfl1_tblnxtchg' => '1 ',
                'sfl1_rrn' => '1',
                'sfl1_top' => '1',
                'sfl1_page' => '1',
                'lock' => 'LOCK',
            ),
        ));
    }
}
