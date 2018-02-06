<?php

namespace Matyotools\TimesheetBundle\Services;

use Harvest\Model\Project;
use Harvest\Model\Range;
use Harvest\Model\Result;
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
    protected $truncate;
    protected $truncateRand;
    protected $freeTimeProjectId;
    protected $freeTimeTaskId;
    protected $vacationTimeProjectId;

    public function __construct(HarvestApp $api, $user, $password, $account, $mode, $truncate, $truncateRand, $freeTimeProjectId, $freeTimeTaskId, $vacationTimeProjectId)
    {
        $this->api = $api->getApi();
        $this->user = $user;
        $this->password = $password;
        $this->account = $account;
        $this->mode = $mode;
        $this->truncate = floatval($truncate);
        $this->truncateRand = floatval($truncateRand);
        $this->freeTimeProjectId = $freeTimeProjectId;
        $this->freeTimeTaskId = $freeTimeTaskId;
        $this->vacationTimeProjectId = $vacationTimeProjectId;
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
     * @return Project[]
     */
    public function getProjects()
    {
        /** @var Result $data */
        $data = $this->api->getProjects();

        $projects = array();

        foreach ($data->get('data') as $project) {
            $projects[$project->get('id')] = $project;
        }

        return $projects;
    }

    public function getProject($id)
    {
        return $this->api->getProject($id);
    }

    public function getEntry($id)
    {
        return $this->api->getEntry($id);
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


        $range = new Range($from->format("Ymd"), $to->format("Ymd"));
        $entries = $this->api->getUserEntries($user_id, $range);

        /** @var DayEntry[] $days */
        $days = array();
        if ($entries->isSuccess()) {
            $days = $entries->get('data');
        }

        $days = array_map(function ($day) {
            /** @var DayEntry $day */
            $day->set('hours', floatval($day->get('hours')));

            return $day;
        }, $days);

        return $days;
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

        $group = $this->groupByDays($this->getDays());

        foreach ($group as $spentAt => $days) {
            /** @var DayEntry[] $days */

            // sort by time
            usort($days, function ($dayA, $dayB) {
                /** @var DayEntry $dayA */
                /** @var DayEntry $dayB */
                return $dayA->get('hours') < $dayB->get('hours');
            });

            $min = $this->truncate - $this->truncateRand;
            $max = $this->truncate + $this->truncateRand;

            $projectIds = array();
            foreach ($days as $day) {
                if ($day->get('hours') > 0) {
                    $projectIds[$day->get('project_id')] = $day;
                }
            }

            // truncate projects at 80% of the day
            /** @var DayEntry[] $projectIds */

            // calculate total time
            $totalHours = array_reduce($projectIds, function ($carry, $day) {
                /** @var DayEntry $day */
                return $carry + $day->get('hours');
            }, 0);
            if ($totalHours > $max * 0.8) {
                $realTruncateHours = $min + ($max - $min) * lcg_value();
                $totalTruncateHours = min($realTruncateHours * 0.8, $totalHours);
                $totalHoursRatio = $totalTruncateHours / $totalHours;
                foreach ($projectIds as $day) {
                    $day->set("hours", $day->get('hours') * $totalHoursRatio);
                    $this->api->updateEntry($day);
                }
            }

            //special case if we are in vacation : then make it the whole day
            if (isset($projectIds[$this->vacationTimeProjectId])) {
                foreach ($days as $day) {
                    if($day->get('project_id') == $this->vacationTimeProjectId) {
                        $day->set("hours", $this->truncate);
                        $this->api->updateEntry($day);
                    } else {
                        $this->api->deleteEntry($day->get('id'));
                    }
                }
            }

            // calculate total time
            $totalHours = array_reduce($days, function ($carry, $day) {
                /** @var DayEntry $day */
                return $carry + $day->get('hours');
            }, 0);

            //tuncate max or add freeTime if less than max
            if ($totalHours > $max) {
                // truncate
                $realHours = $min + ($max - $min) * lcg_value();
                $totalHoursDiff = $totalHours - $realHours;

                foreach ($days as $day) {
                    $hours = $day->get('hours');
                    $hoursDiff = $hours - $totalHoursDiff;

                    if ($hoursDiff <= 0) {
                        $day->set('hours', 0);
                        $this->api->deleteEntry($day->get('id'));
                        $totalHoursDiff -= $hours;
                    } else {
                        $day->set('hours', $hoursDiff);
                        $this->api->updateEntry($day);
                        $truncated[] = $day;

                        break;
                    }
                }
            } else if ($totalHours < $min && $this->freeTimeProjectId && $this->freeTimeTaskId) {
                //append free time
                $realHours = $min + ($max - $min) * lcg_value();
                $totalHoursDiff = $realHours - $totalHours;

                $day = new DayEntry();
                $day->set("hours", $totalHoursDiff);
                $day->set("project_id", $this->freeTimeProjectId);
                $day->set("task_id", $this->freeTimeTaskId);
                $day->set("spent_at", $spentAt);
                $this->api->createEntry($day);

                $truncated[] = $day;
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
                    $carry = clone $item;
                } else {
                    $carry->set('hours', $carry->get('hours') + $item->get('hours'));
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

            $min = $this->truncate - $this->truncateRand;
            $max = $this->truncate + $this->truncateRand;

            if ($hours < $min) $hours = '<less>' . $hours . 'H</less>';
            elseif ($hours <= $max) $hours = '<ok>' . $hours . 'H</ok>';
            else $hours = '<more>' . $hours . 'H</more>';

            $lines[] = implode("\t", array(
                $this->getUrl('time/day/' . $day->format('Y') . '/' . $day->format('m') . '/' . $day->format('d') . '/' . $data->get('user-id')),
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
