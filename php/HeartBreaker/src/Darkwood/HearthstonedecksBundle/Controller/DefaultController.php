<?php

namespace Darkwood\HearthstonedecksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DarkwoodHearthstonedecksBundle:Default:index.html.twig', array('name' => $name));
    }
}
