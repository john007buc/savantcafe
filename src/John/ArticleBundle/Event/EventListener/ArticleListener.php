<?php
namespace John\ArticleBundle\Event\EventListener;

use Doctrine\ORM\EntityManager;
use John\ArticleBundle\Event\ArticleEvent;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;


class ArticleListener
{
    protected $container;
    protected $authorEmail;
    protected $authorName;
    protected $article;
    protected $em;

    public function __construct(Objectmanager $em,ContainerInterface $container){
        $this->container=$container;
        $this->em=$em;
    }

    public function setPictureFillTags(ArticleEvent $articleEvent)
    {

        $this->authorEmail=$articleEvent->getAuthorEmail();
        $this->authorName=$articleEvent->getAuthorName();
        $this->article=$articleEvent->getArticle();

        //$this->article->setContent("Modified in article listener");

       $newContent=preg_replace_callback("/<img\s*?(alt=['\"].*?['\"])*\s*src=['\"](.+?)['\"].*?>/i",array($this,"replaceImages"),$this->article->getContent());

       /* dump(preg_match("/<img\s*?(alt=['\"].*?['\"])*\s*src=['\"](.+?)['\"].*?>/i",$this->article->getContent()));exit();*/



        $this->article->setContent($newContent);
        $this->em->persist($this->article);
        $this->em->flush();
    }


    public function replaceImages($matches)
    {



       /*$pictureTag='<picture>
	   <source srcset="examples/images/extralarge.jpg" media="(min-width: 1000px)">
	   <source srcset="examples/images/large.jpg" media="(min-width: 800px)">
	   <img srcset="examples/images/medium.jpg" alt="A giant stone face at The Bayon temple in Angkor Thom, Cambodia">
       </picture>';

      return $pictureTag;*/

        // as usual: $matches[0] is the complete match
        // $matches[1] the match for the first subpattern
        // $matches[2] the match for the second subpattern (the path)

     /*-- get the width of the image --*/
      $image_web_path=strstr($matches[2],"uploads");

     /*-- get the name of the image+extension--*/
     $imgName=pathinfo($image_web_path,PATHINFO_BASENAME);

      /*-- get the base directory (uploads/article_images/year/month/) of the image--*/
      /*-- in this directory will be created folders:small,medium and large for storing responsive images*/

      //$image_base_directory=strstr($image_web_path,"original",true);
      //ATTENTION! in some situation is needed to remove "/" from the beginning
        $image_base_directory=ltrim(strstr($matches[2],"original",true),'/');

     /*-- get the width and height of the image and set the filters used by liip-imagine-bundle*/
      list($width,$height)=getimagesize($image_web_path);

     /*---set the filters-----*/

        if($width>=640){
            $filters=array(
                'small'=>'web_min',
                'medium'=>'web_medium',
                'large'=>'web_large'
            );
        }elseif($width>=480){
            $filters=array(
                'small'=>'web_min',
                'medium'=>'web_medium',
            );

        }elseif($width>=320){
            $filters=array(
                'small'=>'web_min',
            );
        }else{
            $filters=array(
                'small'=>'my_thumb',
            );
        }


      //for every match this function has to
      // 1. make responsive images
      // 2. return the picture tag used by picturefill.js

      //1. Make responsive images

        $this->makeResponsiveImages($filters, $image_web_path, $image_base_directory,$imgName);

      //2 return the picture tag
        /*-- $matches[1]=alt content --*/

        $picTag=$this->getPictureHtml(count($filters), $image_base_directory,$imgName,$matches[1]);

      return $picTag;


    }



    public function makeResponsiveImages($filters, $image_web_path,$image_base_directory,$imgName)
    {
       // $web_path=strstr( $image_web_path,"original",true);
       // dump($image_web_path);
       // dump($image_base_directory);
       // dump($web_path);
       // dump($_SERVER['DOCUMENT_ROOT']);exit();

        $absolute_dir_path=$_SERVER['DOCUMENT_ROOT']."/".$image_base_directory;

        try{
                foreach($filters as $type=>$filter){
                    $container = $this->container;                                  // the DI container
                    $dataManager = $container->get('liip_imagine.data.manager');    // the data manager service
                    $filterManager = $container->get('liip_imagine.filter.manager');// the filter manager service
                    $image = $dataManager->find($filter,$image_web_path);            // find the image and determine its type
                    $response = $filterManager->applyFilter($image, $filter);
                    $binary = $response->getContent();

                    //create the upload directory and write the new image
                    //$upload_dir = $image_base_directory.$type;
                    $upload_dir =$absolute_dir_path.$type;
                    if(!is_dir($upload_dir)){
                        mkdir($upload_dir,0777, true);
                    }
                   // dump(  $image_base_directory);exit();
                    //dump($upload_dir."/".$imgName);exit();
                    //$f = fopen($upload_dir."/".$imgName, 'w');  // create image file





                        if (!$handle = fopen($upload_dir."/".$imgName, 'w+')) {

                            exit;
                        }


                        if (fwrite($handle, $binary) === FALSE) {
                            dump("Cannot write to file ");
                            exit;
                        }


                        fclose($handle);






                    //fwrite($f, $binary);                         // write the image
                    //sleep(1);
                    //fclose($f);
                }

                return true;

            }catch(Exception $ex){

                  dump($ex);exit();
        }

    }

    public function getPictureHtml($filters_number,$image_base_directory,$imageName,$alt)
    {
        $image_base_directory="/".$image_base_directory;

        switch ($filters_number){
            case 3:
                $small_path= $image_base_directory."small/".$imageName;
                $medium_path= $image_base_directory."medium/".$imageName;
                $large_path= $image_base_directory."large/".$imageName;
                break;
            case 2:
                $small_path= $image_base_directory."small/".$imageName;
                $medium_path=$large_path= $image_base_directory."medium/".$imageName;
                break;
            case 1:
                $small_path=$medium_path=$large_path= $image_base_directory."small/".$imageName;
        }

        $pictureTag= <<<END_TEXT
          <picture>
	        <source srcset='$large_path' media='(min-width: 1000px)'>
	        <source srcset='$medium_path' media='(min-width: 500px)'>
	        <img srcset='$small_path' $alt>
            </picture>
END_TEXT;

       return  $pictureTag;

    }


    public function sendAuthorEmail()
    {
        if( $this->authorEmail){

            $message = \Swift_Message::newInstance()
                ->setSubject('SavantCafe publishing')
                ->setFrom('admin@savantcafe.com')
                ->setTo('john007buc@yahoo.com')
                ->setBody(
                    $this->container->get('templating')->render("JohnArticleBundle::email.html.twig",array(
                        'author'=>$this->authorName,
                        'article'=>$this->article
                    )),'text/html'
                );

            $this->container->get("mailer")->send($message);
        }


    }




}
