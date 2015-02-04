<?php

namespace John\ArticleBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator;


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

    public function uploadAction(Request $request)
    {

        $uploadedFile = $this->getRequest()->files->get('upload');

        //validate this image
        $validator=$this->get("validator");
        $image_constraint = new Image();
       $image_constraint->mimeTypes=array('image/gif','image/png','image/jpeg');


        $image_constraint->maxSize='2k';



            $error =$validator->validateValue($uploadedFile,$image_constraint);

if(!empty($error)){
    return new Response($error);
}

        $tmpFolderPathAbs = $this->get('kernel')->getRootDir()."/../web/uploads/article_images/"; // folder to store unfiltered temp file
        $hash = sha1(uniqid(mt_rand(), true));
        $tmpImageName = $hash .".". $uploadedFile->guessExtension();

        $uploadedFile->move($tmpFolderPathAbs, $tmpImageName);
        $web_path = "uploads/article_images/".$tmpImageName;

        $srcPath="";
        foreach(['web_medium','web_large'] as $filter)
        {

            try{
                $imagemanagerResponse = $this->container
                    ->get('liip_imagine.controller')
                    ->filterAction(
                        $this->getRequest(),
                        $web_path,
                        $filter
                    );
            }catch (\Exception $e)
            {
                return new Response("Update Error");
            }


            $cacheManager = $this->container->get('liip_imagine.cache.manager');

            $srcPath .= $cacheManager->getBrowserPath($web_path, $filter).";";

        }





        return new Response("Uploaded OK");

    }


}