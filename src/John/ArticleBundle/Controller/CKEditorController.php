<?php

namespace John\ArticleBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CKEditorController extends Controller
{


    public function browseAction(Request $request)
    {
        $callback = $request->query->get('CKEditorFuncNum');
        $images_directory=$this->get('kernel')->getRootDir()."/../web/uploads/article_images";

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

    public function uploadAction()
    {
        //move_uploaded_file($_FILES["upload"]["tmp_name"],$this->get('kernel')->getRootDir()."/../web/uploads/article_images/".$_FILES["upload"]["name"]);
       // return new Response("image uploaded");
        $filter='strip';
        $container = $this->container;
        $dataManager = $container->get('liip_imagine.data.manager');    // the data manager service
        $filterManager = $container->get('liip_imagine.filter.manager'); // the filter manager service
       //$the_image_loc=$_FILES["upload"]["tmp_name"];


        move_uploaded_file($_FILES["upload"]["tmp_name"],$this->get('kernel')->getRootDir()."/../web/media/temp/".$_FILES["upload"]["name"]);
        $image = $dataManager->find($filter,$_FILES["upload"]["name"]);
        //dump($image);exit();// find the image and determine its type
        $response = $filterManager->applyFilter($image, $filter);

        $thumb = $response->getContent();                               // get the image from the response
        $folder=$this->get('kernel')->getRootDir()."/../web/uploads/article_images/".$_FILES["upload"]["name"];
        $f = fopen($folder, 'w');                                        // create thumbnail file
        fwrite($f, $thumb);                                             // write the thumbnail
        fclose($f);

        unlink($this->get('kernel')->getRootDir()."/../web/media/temp/".$_FILES["upload"]["name"]);

        return new Response("image uploaded");

    }


}