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

		$guzzle = new GuzzleClient(array('redirect.disable' => true, 'base_url' => ''));
		$guzzle->getEmitter()->attach($this->history);
    }

	public function login()
	{
		echo 'coucou';
	}
}
