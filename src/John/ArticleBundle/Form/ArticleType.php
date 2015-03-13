<?php
namespace John\ArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use John\ArticleBundle\Form\TagType;
use John\ArticleBundle\Form\ImageType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
                ->add('url','text',array('label'=>"Adresa URL"))
                ->add('categories')

                ->add('featured_image', new ImageType(),array(
                    'required' => false,
                ))

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
                 'error_bubbling'=>false,

                ));

       $builder->addEventListener(FormEvents::PRE_SET_DATA,function(FormEvent $event){
           $article=$event->getData();
           $form=$event->getForm();

           if($article!=null && $article->getPublished())
           {
               $form->add('publish','submit',array('label'=>"Save published article"));
           }else{
               $form
                   ->add('publish','submit',array('label'=>"Publish"))
                   ->add('draft','submit',array('label'=>"Save Draft"))
                   ->add('preview','submit',array('label'=>"Preview"));
           }
       });

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver
            ->setDefaults(array(
            'data_class'=>'John\ArticleBundle\Entity\Article',
            'cascade_validation' => true,
            ));
        /*->setRequired(array(
            'is_published',
        ));
        ->setAllowedTypes(array(
            'is_published' => 'boolean',
        ));*/

    }

    public function getName()
    {
        return "article";
    }
}