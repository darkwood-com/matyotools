<?php

namespace Matyotools\HarvestAppBundle\Services;

use \Harvest\HarvestAPI;

/**
 * Very simple proxy class to HarvestAPI functionality
 */
class HarvestApp
{
    private $harvest;

    /**
     * @param HarvestAPI $harvest HarvestAPI API client instance
     */
    public function __construct(HarvestAPI $harvest, $user, $password, $account, $ssl, $mode)
    {
        $this->harvest = $harvest;

        // Set parameters
        $this->harvest->setUser($user);
        $this->harvest->setPassword($password);
        $this->harvest->setAccount($account);
        $this->harvest->setSSL($ssl);
        $this->harvest->setRetryMode($mode);
    }

    /**
     * Get oAuth client
     *
     * @return HarvestAPI
     */
    public function getApi()
    {
        return $this->harvest;
    }
}
