<?php

namespace Darkwood\HearthbreakerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('HearthbreakerBundle:Default:index.html.twig');
    }
}
