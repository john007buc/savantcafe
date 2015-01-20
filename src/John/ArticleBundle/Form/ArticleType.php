<?php
namespace John\ArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use John\ArticleBundle\Form\TagType;
use John\ArticleBundle\Form\ImageType;

class ArticleType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title','text')
                ->add('content','textarea')
                ->add('url','text')
                ->add('categories')
                ->add('existing_tags','entity',array(
                    'class'=>'JohnArticleBundle:Tag',
                    'property'=>'name',
                    'multiple'=>'true'
                ))
                ->add('tags','collection',array(
                    'type'=>new TagType(),
                    'allow_add'=>true,
                    'allow_delete'=>true,
                   'label'=>false,

                ))
                ->add('featured_image', new ImageType(),array('label'=>'Featured Image'))
                ->add('save','submit');

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'=>'John\ArticleBundle\Entity\Article'
        ));

    }

    public function getName()
    {
        return "article";
    }
}