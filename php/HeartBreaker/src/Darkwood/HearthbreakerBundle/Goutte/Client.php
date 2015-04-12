<?php

namespace Darkwood\HearthbreakerBundle\Goutte;

use Darkwood\HearthbreakerBundle\Subscriber\Cache\CacheStorage;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use Doctrine\Common\Cache\Cache;

class Client extends \Goutte\Client
{
    function __construct(Cache $cache, $ttl)
    {
        parent::__construct();

        $guzzle = $this->getClient();
        //$guzzle->setDefaultOption('debug', true);

        CacheSubscriber::attach($guzzle, array(
            'storage' => new CacheStorage($cache),
            'validate' => false,
            'can_cache' => function () {
                return true;
            },
        ));

        $guzzle->getEmitter()->on(
            'complete',
            function (\GuzzleHttp\Event\CompleteEvent $event) use ($ttl) {
                $response = $event->getResponse();
                $response->setHeader('Cache-Control', 'max-age='.$ttl);
            },
            'first'
        );
    }
}
