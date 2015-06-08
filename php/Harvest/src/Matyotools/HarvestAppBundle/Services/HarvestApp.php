<?php

namespace Matyotools\HarvestAppBundle\Services;

use \Harvest\HarvestApi;

/**
 * Very simple proxy class to HarvestApi functionality
 */
class HarvestApp
{
    private $harvest;

    /**
     * @param HarvestApi $harvest HarvestApi API client instance
     */
    public function __construct(HarvestApi $harvest, $user, $password, $account, $mode)
    {
        $this->harvest = $harvest;

        // Set parameters
        $this->harvest->setUser($user);
        $this->harvest->setPassword($password);
        $this->harvest->setAccount($account);
        $this->harvest->setRetryMode($mode);
    }

    /**
     * Get oAuth client
     *
     * @return HarvestApi
     */
    public function getApi()
    {
        return $this->harvest;
    }
}
