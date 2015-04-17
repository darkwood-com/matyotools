<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Doctrine\Common\Cache\Cache;

class CacheService
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var array
     */
    private $config;

    /**
     * @param Cache $cache
     * @param $config
     */
    public function __construct(Cache $cache, $config)
    {
        $this->cache = $cache;
        $this->config = $config;
    }

    /**
     * @param $id
     * @param $data
     * @param int|string|null $lifeTime
     */
    public function fetch($id, $data, $lifeTime = null, $rand = true)
    {
		$id = md5($id);
        $d = $this->cache->fetch($id);
        if ($d === false) {
            $d = $data();

            if (!is_null($lifeTime)) {
                if (isset($this->config['keys'][$lifeTime])) {
                    $lifeTime = $this->config['keys'][$lifeTime];
                }

                if ($rand) {
                    $rand = $this->config['random_percent'];
                    $lifeTime = round($lifeTime * (1 + rand(-1 * $rand, $rand) / 100));
                }

                $this->cache->save($id, $d, $lifeTime);
            }
        }

        return $d;
    }
}
