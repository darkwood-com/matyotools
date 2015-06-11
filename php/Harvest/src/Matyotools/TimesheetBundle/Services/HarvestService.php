<?php

namespace Matyotools\TimesheetBundle\Services;

use Harvest\Model\Range;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

use \Matyotools\HarvestAppBundle\Services\HarvestApp;
use \Harvest\HarvestApi;
use \Harvest\Model\DayEntry;
use \Harvest\Model\DailyActivity;

class HarvestService
{
    /**
     * @var HarvestApi
     */
    protected $api;

    protected $user;
    protected $password;
    protected $account;
    protected $mode;
    protected $truncateMax;
    protected $truncateRand;

    public function __construct(HarvestApp $api, $user, $password, $account, $mode, $truncateMax, $truncateRand)
    {
        $this->api = $api->getApi();
        $this->user = $user;
        $this->password = $password;
        $this->account = $account;
        $this->mode = $mode;
        $this->truncateMax = $truncateMax;
        $this->truncateRand = $truncateRand;
    }

    public function getMyUserId()
    {
        $daily = $this->api->getDailyActivity();
        /** @var DailyActivity $activity */
        $activity = $daily->get('data');
        /** @var DayEntry[] $days */
        $days = $activity->get('day_entries');

        foreach ($days as $day) {
            return $day->get('user-id');
        }

        return null;
    }

	/**
	 * @param \DateTime $from
	 * @param \DateTime $to
	 * @return DayEntry[]
	 * @throws \Harvest\Exception\HarvestException
	 */
	public function getRangeDays($from, $to)
	{
		$user_id = $this->getMyUserId();


		$range = new Range($from->format( "Ymd" ), $to->format( "Ymd" ));
		$entries = $this->api->getUserEntries($user_id, $range);

		return $entries->isSuccess() ? $entries->get('data') : array();
	}

    /**
     * @return DayEntry[]
     */
    public function getDays()
    {
		$to = new \DateTime();
		$from = new \DateTime("-60 day"); //since 60 days

        return $this->getRangeDays($from, $to);
    }

    /**
     * @param DayEntry[] $days
     */
    public function groupByDays($days)
    {
        $group = array();
        foreach ($days as $day) {
            $group[$day->get('spent-at')][] = $day;
        }

        return $group;
    }

    public function truncate()
    {
        $truncated = array();

        $days = $this->getDays();
        $group = $this->groupByDays($days);

        foreach ($group as $days) {
            $totalHours = 0;
            foreach ($days as $day) {
                /** @var DayEntry $day */
                $hours = floatval($day->get('hours'));
                $totalHours += $hours;

                if (!is_null($totalHours) && $totalHours > $this->truncateMax) {
                    //truncate this entry
                    $hours = $hours - ($totalHours - $this->truncateMax);
                    $rand = mt_rand(max(0, ($hours - $this->truncateRand) * 100), $hours * 100) / 100;
                    $day->set('hours', $rand);
                    $this->api->updateEntry($day);
                    $truncated[] = $day;

                    $totalHours = null;
                } elseif (is_null($totalHours)) {
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

        foreach ($running as $run) {
            $this->api->toggleTimer($run->get('id'));
        }

        return $running;
    }

    public function stats()
    {
        $days = $this->getDays();
        $group = $this->groupByDays($days);

        foreach ($group as $i => $days) {
            $group[$i] = array_reduce($days, function ($carry, $item) {
                /** @var DayEntry $carry */
                /** @var DayEntry $item */
                if (is_null($carry)) {
                    return $item;
                } elseif (!is_null($item)) {
                    $carry->set('hours', $carry->get('hours') + $item->get('hours'));

                    return $carry;
                }

                return $carry;
            });
        }

        return $group;
    }

    /**
     * @return DayEntry[]
     */
    public function running()
    {
        $days = $this->getDays();

        $running = array();

        foreach ($days as $day) {
            $timer = $day->get('timer-started-at');
            if (!empty($timer)) {
                $running[] = $day;
            }
        }

        return $running;
    }

    public function getUrl($url)
    {
        $http = "http://";

        return $http . $this->account . ".harvestapp.com/" . $url;
    }

    public function display($data, OutputInterface $output)
    {
        $output->getFormatter()->setStyle('ok', new OutputFormatterStyle('green'));
        $output->getFormatter()->setStyle('less', new OutputFormatterStyle('yellow'));
        $output->getFormatter()->setStyle('more', new OutputFormatterStyle('red'));

        $lines = array();

        if ($data instanceof DayEntry) {
            $day = new \DateTime($data->get('created-at'));

            $hours = $data->get('hours');
            if($hours < $this->truncateMax - $this->truncateRand) $hours = '<less>'.$hours.'H</less>';
            elseif($hours <= $this->truncateMax) $hours = '<ok>'.$hours.'H</ok>';
            else $hours = '<more>'.$hours.'H</more>';

            $lines[] = implode("\t\t", array(
                $this->getUrl('time/day/'.$day->format('Y').'/'.$day->format('m').'/'.$day->format('d').'/'.$data->get('user-id')),
                $hours,
            ));
        } elseif (is_array($data)) {
            foreach ($data as $d) {
                $ll = $this->display($d, $output);
                foreach ($ll as $l) {
                    $lines[] = $l;
                }
            }
        }

        return $lines;
    }
}
