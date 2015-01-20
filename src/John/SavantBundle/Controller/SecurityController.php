<?php

namespace John\SavantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use John\SavantBundle\Entity\User;
use John\SavantBundle\Form\RegisterType;

class SecurityController extends Controller
{
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }


        /*=========================Register=========================*/
        $user= new User();
        $register_form = $this->createForm(new RegisterType(),$user,array(
            "method"=>"POST",
            "attr"=>array(
                "id"=>"register_form"
            )
        ));


        $register_form->handleRequest($request);

        if( $register_form->isValid() )
        {

            if($this->verify_captcha( $register_form)){

                $password= $register_form->get('plainPassword')->getData();

                $encoded_password=$this->encode_password($user,$password);

                $user->setPassword($encoded_password);
                $user->setRoles(array("ROLE_USER"));

                $em=$this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                //authenticate user and reidrect to user profile page

                $this->authenticateUser($user);
                //redirect
                return $this->redirect($this->generateUrl('john_savant_profile'));

            }else{
                $this->get('session')->getFlashBag()->add('security_msg','Invalid security code');
            }


            /*$session_code=$this->get('session')->get('secure_code');
            $form_code=$form->get('captcha')->getData();
            $match=( $session_code==$form_code)?("codes match"):("Codes doesn't match");

            return new Response('<html><body>Hello '.$match.'!</body></html>');*/
        }


        return $this->render(
            'JohnSavantBundle:Security:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
                'register_form'=>$register_form->createView()
            )
        );


    }

    public function verify_captcha($form)
    {
        $session_code=$this->get('session')->get('secure_code');
        $form_code=$form->get('captcha')->getData();

        return ($session_code==$form_code)?true:false;

    }

    public function encode_password(User $user, $plain_passord)
    {
        $factory=$this->get('security.encoder_factory');
        $encoder=$factory->getEncoder($user);

        return $encoder->encodePassword($plain_passord,$user->getSalt());

    }

    private function authenticateUser(User $user)
    {
        $firewall_name="secured_area";
        $token=new UsernamePasswordToken($user,null,$firewall_name,$user->getRoles());
        $this->container->get("security.context")->setToken($token);
    }
}
