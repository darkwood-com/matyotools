<?php

namespace Matyotools\DominoBundle\Services;

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

    public function __construct($user, $password)
    {
		$this->history = new History();

		$this->client = new GuzzleClient();
		$this->client->getEmitter()->attach($this->history);
    }

	public function login()
	{
		$loginUrl = 'https://dominoweb.domino-info.fr:7001/cgiphl/pw_login.pgm';
		$this->client->post($loginUrl, array(
			'body' => array(
				'function' => 1,
				'name1' => $this->user,
				'name2' => $this->password,
				'apkode' => '',
			)
		));
	}
}
