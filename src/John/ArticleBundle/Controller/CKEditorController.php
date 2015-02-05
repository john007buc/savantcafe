<?php

namespace John\ArticleBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator;


class CKEditorController extends Controller
{


    public function browseAction(Request $request)
    {
        $callback = $request->query->get('CKEditorFuncNum');
        $images_directory=$this->get('kernel')->getRootDir()."/../web/media/cache/my_thumb/uploads/article_images";

        $image_types = array(
            'gif' => 'image/gif',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
        );

        $images = array_diff(scandir($images_directory), array('..', '.'));

        $i=array();
        foreach ($images as $entry) {
            if (!is_dir($entry)) {

                   $i[]=$entry;

            }
        }
           return $this->render("JohnArticleBundle:CKEditor:browse.html.twig",array(
               "images"=>$i,
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
        $web_path = "uploads/tmp/".$imgHash;
        $image_temp_absolute_path=$tmpFolder.$imgHash;
        $uploadedFile->move($tmpFolder, $imgHash);

        //make upload dir from current year and month
        date_default_timezone_set('Europe/Bucharest');
        $year = date("Y");
        $month= date("m");
        $upload_path=$this->get('kernel')->getRootDir()."/../web/uploads/".$year."/".$month;
        if(!is_dir($upload_path)){
            mkdir($upload_path,0777, true);
        }

        list($width,$height)=getimagesize($image_temp_absolute_path);   //get the size of the file and apply the filter
        $filter=($width>500)?"web_medium":"strip_filter";

        $container = $this->container;                                  // the DI container
        $dataManager = $container->get('liip_imagine.data.manager');    // the data manager service
        $filterManager = $container->get('liip_imagine.filter.manager');// the filter manager service

        try{
            $image = $dataManager->find($filter, $web_path);            // find the image and determine its type
            $response = $filterManager->applyFilter($image, $filter);
            $binary = $response->getContent();                           // get the image from the response

            $f = fopen($image_path=$upload_path."/".$imgHash, 'w');  // create image file
            fwrite($f, $binary);                         // write the image
            fclose($f);

            //write image path in databse

            unlink($image_temp_absolute_path);            //delete the old image from temp folder
            return new Response("Updated OK");
        }catch(Exception $ex)
        {
            return new Response("Update error");
        }
    }


}