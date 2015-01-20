<?php
namespace John\SavantBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class RegisterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder->add('first_name','text');
            $builder->add('last_name','text');
            $builder->add('email','email');
            $builder->add('plainPassword','repeated',array(
               'type'=>'password',
               'invalid_message'=>'Parolele nu se potrivesc'
            ));
            $builder->add('captcha','text',array(
               'label'=>'Security code',
               'mapped'=>false
            ));
            $builder->add('Submit','submit');

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
           'data_class'=>'John\SavantBundle\Entity\User',
            'validations_groups'=>array('Default','register')
        ));


    }

    public function getName()
    {
        return 'register_form';
    }





}


