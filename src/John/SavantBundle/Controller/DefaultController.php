<?php

namespace John\SavantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name="john")
    {
        return $this->render('JohnSavantBundle:Default:index.html.twig', array('name' => $name));
    }
}
