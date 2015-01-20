<?php
namespace John\SavantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use John\SavantBundle\Entity\User;
use John\SavantBundle\Form\ProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use John\SavantBundle\Form\Model\ChangePassword;
use John\SavantBundle\Form\ChangePasswordType;
class ProfileController extends Controller
{

    public function indexAction()
    {


       return $this->render("JohnSavantBundle:Profile:index.html.twig");



    }

    public function editAction(Request $request,$id)
    {
        //$user=$this->getUser();
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository('JohnSavantBundle:User')->find($id);

        $profile_form=$this->createForm(new ProfileType(),$user,array(
            'action'=>$this->generateUrl('john_savant_profile_edit',array('id'=>$user->getId())),
            'method'=>'POST',
             'attr'=>array(
                 'class'=>'profile_form'
             )
        ));
        $profile_form->handleRequest($request);
        if($profile_form->isValid())
        {
           $em->persist($user);
           $em->flush();
           $response = new Response("Datele au fost salvate");
           $response->send();
            die();

        }


        return $this->render("JohnSavantBundle:Profile:edit.html.twig",array(
            'profile_form'=>$profile_form->createView()
        ));
    }

    public function changePasswordAction(Request $request)
    {

        $passwords=new ChangePassword();
        $form=$this->createForm(new ChangePasswordType(),$passwords);
        $form->handleRequest($request);

        if($form->isValid())
        {
            //save current password in  data base


            $em=$this->getDoctrine()->getManager();
            $user=$em->getRepository('JohnSavantBundle:User')->find( $this->getUser()->getId());

            $newPass=$this->encodePass($user,$passwords->getPlainPassword());
            $user->setPassword($newPass);
            $em->persist($user);
            $em->flush();
        }

        return $this->render("JohnSavantBundle:Profile:change_password.html.twig",array(
           'change_password_form'=>$form->createView()
        ));

    }

    private function encodePass(User $user, $plain_pass)
    {
        /*$factory=$this->container->get("security.encoder_factory");
        $encoder=$factory->getEncoder($user);

        return $encoder->encodePassword($plain_pass, $user->getSalt());*/
        $encoder = $this->get('security.encoder_factory')
            ->getEncoder($user);

        return $encoder->encodePassword($plain_pass, $user->getSalt());
    }




}