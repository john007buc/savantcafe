<?php

namespace John\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('JohnAdminBundle:Default:index.html.twig', array('name' => "admin"));
    }
}
