<?php

namespace John\ArticleBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator;
use John\ArticleBundle\Entity\Format;
use John\ArticleBundle\Entity\Media;

class CKEditorController extends Controller
{


    public function browseAction(Request $request)
    {
        $images = $this->getDoctrine()->getRepository("JohnArticleBundle:Media")->getAuthorImages($this->getUser()->getId());

        return $this->render("JohnArticleBundle:CKEditor:browse.html.twig",array(
               "images"=>$images,
               'callback'=>$callback
           ));
    }

    public function uploadAction(Request $request)
    {
        $uploadedFile = $this->getRequest()->files->get('upload');
        //validate the image
        $validator=$this->get("validator");
        $image_constraint = new Image();
        $image_constraint->mimeTypes=array('image/gif','image/jpeg');
        $image_constraint->mimeTypesMessage="Error: The accepted formats are: gif,jpeg";
        $image_constraint->maxSize='2000k';
        $errors =$validator->validateValue($uploadedFile,$image_constraint);

        if(count($errors)>0){
            $error_message="";
                foreach($errors as $error)
                {
                    $error_message.=$error->getMessage()."##";
                }
            $response =  new Response( $error_message);
            $response->headers->set('Content-Type', 'text/plain');
            return $response;
        }

        //move the uploaded file in a temporary location
        $tmpFolder = $this->get('kernel')->getRootDir()."/../web/uploads/tmp/"; // folder to store unfiltered temp file
        $hash = sha1(uniqid(mt_rand(), true));
        $imgHash = $hash .".". $uploadedFile->guessExtension();
        $temp_path = "uploads/tmp/".$imgHash;
        $image_temp_absolute_path=$tmpFolder.$imgHash;
        $uploadedFile->move($tmpFolder, $imgHash);

        //make upload dir from current year and month
        date_default_timezone_set('Europe/Bucharest');
        $year = date("Y");
        $month= date("m");
        $upload_dir=$this->get('kernel')->getRootDir()."/../web/uploads/".$year."/".$month;
        $image_web_path="uploads/".$year."/".$month."/".$imgHash;
        if(!is_dir($upload_dir)){
            mkdir($upload_dir,0777, true);
        }

        list($width,$height)=getimagesize($image_temp_absolute_path);   //get the size of the file and apply the filter
        $filter=($width>500)?"web_medium":"strip_filter";

        $container = $this->container;                                  // the DI container
        $dataManager = $container->get('liip_imagine.data.manager');    // the data manager service
        $filterManager = $container->get('liip_imagine.filter.manager');// the filter manager service

        try{
            $image = $dataManager->find($filter, $temp_path);            // find the image and determine its type
            $response = $filterManager->applyFilter($image, $filter);
            $binary = $response->getContent();                           // get the image from the response

            $f = fopen($image_root_path=$upload_dir."/".$imgHash, 'w');  // create image file
            fwrite($f, $binary);                         // write the image
            fclose($f);

            //write image path in database
            $em=$this->getDoctrine()->getManager();
            $image_format = $em->getRepository("JohnArticleBundle:Format")->findOneBy(array("name"=>"image"));
            $media = new Media();
            $media->setType($image_format);
            $media->setAuthor($this->getUser());
            $media->setPath($image_web_path);
            $media->setRootPath($image_root_path);
            $em=$this->getDoctrine()->getManager();
            $em->persist($media);
            $em->flush();

            unlink($image_temp_absolute_path);            //delete the old image from temp folder
            return new Response("Updated OK");
        }catch(Exception $ex)
        {
            return new Response("Update error");
        }
    }


}