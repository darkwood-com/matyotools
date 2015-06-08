<?php

namespace Matyotools\DominoBundle\Services;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Response as GuzzleResponse;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Post\PostFile;

class DominoService
{
	/**
	 * @var History
	 */
	protected $history;

	/**
	 * @var Client
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

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;

		$this->history = new History();

		$guzzle = new GuzzleClient();
        $guzzle->getEmitter()->attach($this->history);

        $this->client = new Client();
        $this->client->setClient($guzzle);
    }

	public function login()
	{
		$loginUrl = 'https://dominoweb.domino-info.fr:7001/cgiphl/pw_login.pgm';
		$crawler = $this->client->request('POST', $loginUrl, array(
			'body' => array(
				'function' => 1,
				'name1' => $this->user,
				'name2' => $this->password,
				'apkode' => '',
			)
		));
	}
}
