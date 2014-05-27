<?php

namespace Matyotools\TimesheetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        /** @var \Matyotools\TimesheetBundle\Services\HarvestService $api */
        $api = $this->get('matyotools_timesheet.harvest');
        $days = $api->stats();

        echo implode($api->display($days), "\n");

        return $this->render('MatyotoolsTimesheetBundle:Default:index.html.twig', array());
    }
}
