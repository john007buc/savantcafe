<?php
namespace John\ArticleBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;



use Symfony\Component\Form\AbstractType;

class FilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder,array $options)
    {


            $builder->add('categories','entity',array(
                'class'=>'JohnArticleBundle:Category',
                'property'=>'name',
                'data'=>'slug',
                'mapped'=>false

            ))
                ->add('published','checkbox',array('mapped'=>false))
                ->add('active','checkbox',array('mapped'=>false))
                ->add('save','submit',array('label'=>'Filter'));
    }

    public function getName()
    {
        return 'filter_articles';
    }
}