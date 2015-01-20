<?php
namespace John\SavantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder,array $options)
    {
        $builder->add('first_name','text')
                ->add('last_name','text')
                ->add('file','file',array('required'=>false))
                ->add('interested_fields')
                ->add('Submit','submit');
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
           'data_class'=>'John\SavantBundle\Entity\User',
            'validations_groups'=>array('profile','Default')
        ));
    }

    public function getName()
    {
        return 'profile_form';
    }

}