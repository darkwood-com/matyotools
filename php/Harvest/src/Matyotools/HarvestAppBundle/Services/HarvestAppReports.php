<?php

namespace Matyotools\HarvestAppBundle\Services;

use \Harvest\HarvestReports;

/**
 * Very simple proxy class to HarvestAPI functionality
 */
class HarvestAppReports
{
    private $reports;

    /**
     * @param HarvestReports $reports HarvestAPI API client instance
     */
    public function __construct(HarvestReports $reports, $user, $password, $account, $ssl, $mode)
    {
        $this->reports = $reports;

        // Set parameters
        $this->reports->setUser($user);
        $this->reports->setPassword($password);
        $this->reports->setAccount($account);
        $this->reports->setSSL($ssl);
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
