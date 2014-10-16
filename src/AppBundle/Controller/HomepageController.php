<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class HomepageController extends Controller
{
    /**
     * @Route("/")
     */
    public function homepageAction()
    {
        $characters = $this->getDoctrine()
            ->getRepository('AppBundle:Character')
            ->findAll();

        return $this->render('Homepage/homepage.html.twig', array(
            'characters' => $characters
        ));
    }
}
