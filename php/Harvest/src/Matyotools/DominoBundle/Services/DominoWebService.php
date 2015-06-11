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
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Message\ResponseInterface;

class DominoWebService
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
     * @var array
     */
    protected $headers;

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

        $this->client = new GuzzleClient(array('defaults' => array('allow_redirects' => false, 'cookies' => true)));
        $this->headers = array(
            "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
            "Accept-Encoding" => "gzip, deflate",
            "Accept-Language" => "fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4,tr;q=0.2",
			"Cache-Control" => "no-cache",
			"Connection" => "keep-alive",
            "User-Agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.81 Safari/537.36",
            "Host" => "dominoweb.domino-info.fr:7001",
            "Origin" => "https://dominoweb.domino-info.fr:7001",
        );
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
            'headers' => array_merge($this->headers, array(
                'Content-type' => 'application/x-www-form-urlencoded;charset=UTF-8',
            )),
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

    public function getData()
    {
        $url = 'https://dominoweb.domino-info.fr:7001/cgiphl/PW_CALL.PGM';
        $url = $url . '?' . http_build_query(array(
            'appli' => 'PHLGIF',
            'prop' => '',
            'num' => '646',
            'WINTITLE' => 'Saisie des temps',
            'PW_TABNUM' => '2',
            'id' => $this->pw_id,
        )/*, null, null, PHP_QUERY_RFC3986*/);
        $response = $this->client->get($url, array(
            'headers' => $this->headers
        ));
        $body = (string) $response->getBody();

        $data = array();

        if(preg_match_all('/(<input[^>]*>)/', $body, $matches)) {
            foreach($matches[1] as $match) {
                if(preg_match('/name=([^ >]*)/', $match, $k) && preg_match('/value=([^ >]*)/', $match, $v)) {
                    $data[$k[1]] = $v[1];
                }
            }
        }

        if(empty($data)) {
            throw new Exception('No data');
        }

		return array('data' => $data, 'url' => $url);
    }

    public function fill()
    {
        $d = $this->getData();

        $data = array_merge(array(
            'phl_ini' => '1',
            'pw_lang' => 'FR',
            'pw_id' => 'MKH_MLEDR-2450045',
            'pw_killjob' => '0',
            'phl_job' => 'WEB2450046',
            'phl_dtaq' => 'WEB2450046',
            'phl_user' => 'MKH_MLEDR',
            'phl_joblimit' => '0',
            'phl_jobnum' => '387842',
            'pw_applinum' => 'PHLGIF',
            'pw_applilib' => 'PHLGIF',
            'pw_version' => '1433094444',
            'pw_posy' => '',
            'pw_posx' => '',
            'pw_csry' => '',
            'pw_csrx' => '',
            'pw_wcsry' => '',
            'pw_wcsrx' => '',
            'pw_csrn' => '',
            'pw_csrf' => '',
            'pw_key' => '',
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
            /*'f1_colnum' => '1  2  3  4  5  6  7  8  9  10 11 12 13',
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
            'f1_1_88' => '7',
            'f1_1_89' => '840',
            'f1_1_90' => '8',
            'f1_1_91' => '6',
            'f1_1_92' => '20',
            'f1_1_93' => '15',
            'f1_1_94' => '13',
            'f1_1_95' => '6',
            'f1_1_96' => '20',
            'f1_1_97' => '15',
            'f1_1_98' => '127',
            'f1_1_99' => '1',
            'f1_1_100' => '78194',
            'f1_1_101' => '20',
            'f1_1_102' => '25',
            'f1_1_103' => '5,00',
            'f1_1_111' => '78194-Scania Mag 26',
            'f1_1_112' => '20-INTÉGRATION ET DÉV',
            'f1_1_113' => '25-DÉVELOPPEUR SR',
            'f1_1_114' => '0',
            'f1_1_115' => '.75',
            'f1_1_117' => '1,00',
            'f1_1_118' => '1,00',
            'f1_1_119' => '1,00',
            'f1_1_120' => '0,75',
            'f1_1_121' => '1,00',
            'sfl1_lineh' => '22',
            'sfl1_tblnxtchg' => '1 ',
            'sfl1_rrn' => '1',
            'sfl1_top' => '1',
            'sfl1_page' => '1',*/
            'lock' => 'LOCK',
        ), $d['data'], array(
            'phl_ini' => '1',
            'pw_lang' => 'FR',
        ));

        $data = array_map(function($d) {
            return trim($d, '"\'');
        }, $data);

        $data = http_build_query($data);
        $data = str_replace(array('%C3%89'), array('%C9'), $data);

        $url = 'https://dominoweb.domino-info.fr:7001/cgiphl/PW_CGI.PGM';
        $response = $this->client->post($url, array(
            'headers' => array_merge($this->headers, array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => $this->pw_id.'=; PW_LOGINJ=MKH_MLEDR',
                'Content-Length' => strlen($data),
                'Referer' => $d['url'],
            )),
            'body' => $data,
        ));
    }
}
