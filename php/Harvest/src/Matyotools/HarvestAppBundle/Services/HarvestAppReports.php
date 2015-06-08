<?php

namespace Matyotools\HarvestAppBundle\Services;

use \Harvest\HarvestReports;

/**
 * Very simple proxy class to HarvestApi functionality
 */
class HarvestAppReports
{
    private $reports;

    /**
     * @param HarvestReports $reports HarvestApi API client instance
     */
    public function __construct(HarvestReports $reports, $user, $password, $account, $mode)
    {
        $this->reports = $reports;

        // Set parameters
        $this->reports->setUser($user);
        $this->reports->setPassword($password);
        $this->reports->setAccount($account);
        $this->reports->setRetryMode($mode);
    }

    /**
     * Get oAuth client
     *
     * @return HarvestReports
     */
    public function getApi()
    {
        return $this->reports;
    }
}
