<?php

namespace Matyotools\TimesheetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        /**
         * @var \HarvestAPI $api
         */
        $api = $this->get('harvest_app')->getApi();
        $projects = $api->getDailyActivity();


        return $this->render('MatyotoolsTimesheetBundle:Default:index.html.twig', array());
    }
}
