<?php
namespace John\ArticleBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use John\ArticleBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;

class FilterType extends AbstractType
{

    protected  $em;
    public function __construct(EntityManager $manager)
    {
        $this->em=$manager;
    }

    protected function getCategories()
    {

       return  $this->em->getRepository("JohnArticleBundle:Category")->getSlugNamePairs();
    }

    public function buildForm(FormBuilderInterface $builder,array $options)
    {


            $builder->add('category','choice',array(
                'choices'=>$this->getCategories(),
                'required'=>false,
                'empty_value'=>"Select a category",
                'empty_data'  => null

            ))
                ->add('published','checkbox',array('mapped'=>false,'required'=>false))
                ->add('active','checkbox',array('mapped'=>false,'required'=>false))
                ->add('save','submit',array('label'=>'Filter'));
    }

    public function getName()
    {
        return 'filter_articles';
    }
}