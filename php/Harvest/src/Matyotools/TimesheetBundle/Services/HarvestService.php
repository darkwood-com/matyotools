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
    protected $truncateMax;
    protected $truncateRand;

    public function __construct(\Mattvick\HarvestAppBundle\Services\HarvestApp $api, $user, $password, $account, $ssl, $mode, $truncateMax, $truncateRand)
    {
        $this->api = $api->getApi();
        $this->user = $user;
        $this->password = $password;
        $this->account = $account;
        $this->ssl = $ssl;
        $this->mode = $mode;
        $this->truncateMax = $truncateMax;
        $this->truncateRand = $truncateRand;
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
        $truncated = array();

        $days = $this->getDays();

        $totalHours = 0;
        foreach($days as $day) {
            $hours = floatval($day->get('hours'));
            $totalHours += $hours;

            if(!is_null($totalHours) && $totalHours > $this->truncateMax) {
                //truncate this entry
                $hours = $hours - ($totalHours - $this->truncateMax);
                $rand = mt_rand(($hours - $this->truncateRand) * 100, $hours * 100) / 100;
                $day->set('hours', max(0, $rand));
                $this->api->updateEntry($day);
                $truncated[] = $day;

                $totalHours = null;
            } else if(is_null($totalHours)) {
                //delete tracking when more hour than truncateMax
                $this->api->deleteEntry($day->get('id'));
            }
        }

        return $truncated;
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
