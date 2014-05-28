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

    public function getMyUserId()
    {
        $daily = $this->api->getDailyActivity();
        /** @var \Harvest_DailyActivity $activity */
        $activity = $daily->get('data');
        /** @var \Harvest_DayEntry[] $days */
        $days = $activity->get('day_entries');
        foreach($days as $day)
        {
            return $day->get('user-id');
        }

        return null;
    }

    /**
     * @return \Harvest_DayEntry[]
     */
    public function getDays()
    {
        $user_id = $this->getMyUserId();

        $to = new \DateTime();
        $from = new \DateTime("-60 day"); //since 60 days
        $range = new \Harvest_Range($from, $to);
        $entries = $this->api->getUserEntries($user_id, $range);

        return $entries->get('data');
    }

    /**
     * @param \Harvest_DayEntry[] $days
     */
    public function groupByDays($days)
    {
        $group = array();
        foreach($days as $day)
        {
            $group[$day->get('spent-at')][] = $day;
        }

        return $group;
    }

    public function truncate()
    {
        $truncated = array();

        $days = $this->getDays();
        $group = $this->groupByDays($days);

        foreach($group as $days)
        {
            $totalHours = 0;
            foreach($days as $day) {
                /** @var \Harvest_DayEntry $day */
                $hours = floatval($day->get('hours'));
                $totalHours += $hours;

                if(!is_null($totalHours) && $totalHours > $this->truncateMax) {
                    //truncate this entry
                    $hours = $hours - ($totalHours - $this->truncateMax);
                    $rand = mt_rand(max(0, ($hours - $this->truncateRand) * 100), $hours * 100) / 100;
                    $day->set('hours', $rand);
                    $this->api->updateEntry($day);
                    $truncated[] = $day;

                    $totalHours = null;
                } else if(is_null($totalHours)) {
                    //delete tracking when more hour than truncateMax
                    $this->api->deleteEntry($day->get('id'));
                }
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
        $days = $this->getDays();
        $group = $this->groupByDays($days);

        foreach($group as $i => $days)
        {
            $group[$i] = array_reduce($days, function($carry, $item) {
                /** @var \Harvest_DayEntry $carry */
                /** @var \Harvest_DayEntry $item */
                if(is_null($carry)) {
                    return $item;
                } else if(!is_null($item)) {
                    $carry->set('hours', $carry->get('hours') + $item->get('hours'));
                    return $carry;
                }

                return $carry;
            });
        }

        return $group;
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
            if(!empty($timer)) {
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
