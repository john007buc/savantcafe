<?php
namespace John\ArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;




class ImageType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
      $builder->add('file','file',array("label"=>"Featured Img",'required' => false));

  }


  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
      $resolver->setDefaults(array(
          'data_class'=>'John\ArticleBundle\Entity\Image'
      ));

  }

  public function getName()
  {
      return "Image_form";
  }


}