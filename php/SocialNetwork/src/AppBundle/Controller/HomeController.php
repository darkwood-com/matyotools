<?php

namespace AppBundle\Controller;

use Facebook\Facebook;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function homeAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('AppBundle:Home:index.html.twig', array(
        ));
    }

    /**
     * @Route("/fb-login", name="facebookLogin")
     */
    public function facebookLogin(Request $request)
    {

    }

    public function menuAction()
    {
        /** @var Facebook $facebook */
        $facebook = $this->get('app.facebook');

        $helper = $facebook->getRedirectLoginHelper();
        $permissions = ['email', 'user_likes']; // optional
        $loginUrl = $helper->getLoginUrl($this->generateUrl('facebookLogin', array(), UrlGeneratorInterface::ABSOLUTE_URL), $permissions);

        // replace this example code with whatever you need
        return $this->render('AppBundle:partials:menu.html.twig', array(
            'loginUrl' => $loginUrl,
        ));
    }
}
