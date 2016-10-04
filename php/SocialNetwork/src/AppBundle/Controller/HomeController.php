<?php

namespace AppBundle\Controller;

use Facebook\Facebook;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /** @var Facebook $facebook */
        $facebook = $this->get('app.facebook');
        dump($facebook);

        // replace this example code with whatever you need
        return $this->render('AppBundle:Home:index.html.twig');
    }
}
