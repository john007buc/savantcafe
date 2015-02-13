<?php
namespace John\UsersBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use John\UsersBundle\Form\RegisterType;
use John\UsersBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{

    public function indexAction()
    {
        $user= new User();
        $form = $this->createForm(new RegisterType(),$user,array(
            "action"=>$this->generateUrl('john_users_register')
        ));
        $request=$this->getRequest();



        $form->handleRequest($request);

        if($form->isValid() )
        {

           if($this->verify_captcha($form)){

               $password=$form->get('plainPassword')->getData();

               $encoded_password=$this->encode_password($user,$password);

               $user->setPassword($encoded_password);
               $user->setRoles(array("ROLE_USER"));

               $em=$this->getDoctrine()->getManager();
               $em->persist($user);
               $em->flush();

               return $this->redirect($this->generateUrl("john_users_profile"));

           }else{
               $this->get('session')->getFlashBag()->add('security_msg','Invalid security code');
           }


            /*$session_code=$this->get('session')->get('secure_code');
            $form_code=$form->get('captcha')->getData();
            $match=( $session_code==$form_code)?("codes match"):("Codes doesn't match");

            return new Response('<html><body>Hello '.$match.'!</body></html>');*/
        }

        return $this->render('JohnUsersBundle:Register:index.html.twig',array(
           'register_form'=>$form->createView()
        ));

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



}
?>