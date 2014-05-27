<?php

namespace Matyotools\TimesheetBundle\Services;

class HarvestService
{
    /**
     * @var \HarvestAPI
     */
    protected $api;

    protected $user;
    protected $password;
    protected $account;
    protected $ssl;
    protected $mode;

    public function __construct(\Mattvick\HarvestAppBundle\Services\HarvestApp $api, $user, $password, $account, $ssl, $mode)
    {
        $this->api = $api->getApi();
        $this->user = $user;
        $this->password = $password;
        $this->account = $account;
        $this->ssl = $ssl;
        $this->mode = $mode;
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
        $days = $this->getDays();
        $i = 0;
    }

    public function stop()
    {
        $running = $this->running();

        foreach($running as $run)
        {
            $this->api->toggleTimer($run->get('id'));
        }
    }

    public function stats()
    {
        echo 'Stats';
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

    public function getUrl($url)
    {
        $http = "http://";
        if( $this->ssl ) {
            $http = "https://";
        }
        return $http . $this->account . ".harvestapp.com/" . $url;
    }

    public function display($data)
    {
        $lines = array();

        if($data instanceof \Harvest_DayEntry) {
            $day = new \DateTime($data->get('created-at'));
            $lines[] = $this->getUrl('time/day/'.$day->format('Y').'/'.$day->format('m').'/'.$day->format('d').'/'.$data->get('user-id')) . "\t\t" . $data->get('hours').'H';
        } else if(is_array($data)) {
            foreach($data as $d) {
                $ll = $this->display($d);
                foreach($ll as $l) {
                    $lines[] = $l;
                }
            }
        }

        return $lines;
    }
}
