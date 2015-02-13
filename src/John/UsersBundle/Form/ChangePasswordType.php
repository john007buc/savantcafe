<?php
namespace John\UsersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChangePasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('old_password','text')
                ->add('plainPassword','repeated',array(
                    'type'=>'password',
                    'invalid_message'=>'PArolele nu se potrivesc'
            ))->add('submit','submit');
    }


   public function setDefaultOptions(OptionsResolverInterface $resolver)
   {
       $resolver->setDefaults(array(
           'data_class'=>"John\UsersBundle\Form\Model\ChangePassword"
       ));
   }


    public function getName()
    {
        return "user_password";
    }

}