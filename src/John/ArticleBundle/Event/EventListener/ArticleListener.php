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

       dump($newContent);exit();

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


      $rel_path=strstr($matches[2],"uploads");
      list($width,$height)=getimagesize($rel_path);
       // dump($width);exit();

      dump($this->getPictureTag($matches[2],$matches[1]));exit();


    }

    public function getPictureTag($image,$alt)
    {
        //get the path relative to web
        //ex: uploads/2015/04/original/ec0e28668e44b354b138c5ea768a68c16f29c7cb.jpeg
        $web_path=strstr($image,"uploads");
        list($width,$height)=getimagesize($web_path);

        //get root path
        $path=strstr($image,"original",true);

        //get the name of the image+extension
        $imgName=pathinfo($image,PATHINFO_BASENAME);


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

        if($this->makeResponsiveImages($filters,$web_path)){
            return $this->getPictureHtml(count($filters),$path,$imgName,$alt);
        }else{
            return "IMG ERROR";
        }


    }

    public function makeResponsiveImages($filters,$image)
    {
        //get path without original direcotry
        $path=strstr($image,"original",true);

        //get the name of the image+extension
        $imgName=pathinfo($image,PATHINFO_BASENAME);

            try{

                foreach($filters as $type=>$filter){

                    $container = $this->container;                                  // the DI container
                    $dataManager = $container->get('liip_imagine.data.manager');    // the data manager service
                    $filterManager = $container->get('liip_imagine.filter.manager');// the filter manager service
                    $image = $dataManager->find($filter,$image);            // find the image and determine its type
                    $response = $filterManager->applyFilter($image, $filter);
                    $binary = $response->getContent();

                    $upload_dir = $path.$type;
                    if(!is_dir($upload_dir)){
                        mkdir($upload_dir,0777, true);
                    }
                    $f = fopen($upload_dir."/".$imgName, 'w');  // create image file
                    fwrite($f, $binary);                         // write the image
                    fclose($f);
                }

                return true;

            }catch(Exception $ex)
            {

            }
    }

    public function getPictureHtml($nr,$path,$imageName,$alt)
    {

        switch ($nr){
            case 3:
                $small_path=$path."small/".$imageName;
                $medium_path=$path."medium/".$imageName;
                $large_path=$path."large/".$imageName;
                break;
            case 2:
                $small_path=$path."small/".$imageName;
                $medium_path=$large_path=$path."medium/".$imageName;
                break;
            case 1:
                $small_path=$medium_path=$large_path=$path."small/".$imageName;
        }

        $pictureTag= <<<END_TEXT
          "<picture>
	        <source srcset='$large_path' media='(min-width: 1000px)'>
	        <source srcset='$medium_path' media='(min-width: 500px)'>
	        <img srcset='$small_path' alt=$alt>
            </picture>"
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
