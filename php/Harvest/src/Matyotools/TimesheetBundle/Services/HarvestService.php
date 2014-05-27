<?php

namespace Matyotools\TimesheetBundle\Services;

class HarvestService
{
    /**
     * @var \HarvestAPI
     */
    protected $api;

    public function __construct(\Mattvick\HarvestAppBundle\Services\HarvestApp $api)
    {
        $this->api = $api->getApi();
    }

    /**
     * @return \Harvest_DayEntry[]
     */
    public function getDays()
    {
        $daily = $this->api->getDailyActivity();

        /** @var \Harvest_DailyActivity $activity */
        $activity = $daily->get('data');
        return $activity->get('day_entries');
    }

    public function truncate()
    {
        $projects = $this->api->getDailyActivity();


        $i = 0;
    }

    public function stop()
    {

    }

    public function stats()
    {
        echo 'd';
    }

    /**
     * @return \Harvest_DayEntry[]
     */
    public function running()
    {
        $days = $this->getDays();

        $running = array();

        foreach($days as $day) {
            $timer = $day->get('timer-started-at');
            if(!is_null($timer)) {
                $running[] = $day;
            }
        }

        return $running;
    }
}
