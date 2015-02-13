<?php
namespace John\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use John\UsersBundle\MyClasses\Captcha;

class CaptchaController extends Controller
{

    public function indexAction($random)
    {
          $session=$this->get('session');
          $captcha= Captcha::generate($session);

         ob_start();
          imagejpeg($captcha);
          imagedestroy($captcha);
          $secure_img=ob_get_clean();
        //ob_get_clean();
        $response=new Response($secure_img);
        $response->headers->set("Content-Type","image/jpg");
        $response->send();



    }

}