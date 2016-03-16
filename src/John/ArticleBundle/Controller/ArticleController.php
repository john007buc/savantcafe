<?php
namespace John\ArticleBundle\Controller;

use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use John\ArticleBundle\Form\ArticleType;
use John\ArticleBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use John\ArticleBundle\Form\FilterType;
use John\ArticleBundle\MyClasses\Pagination;
use John\ArticleBundle\Event\ArticleEvents;
use John\ArticleBundle\Event\ArticleEvent;


class ArticleController extends Controller
{

    /*
     * Show user's articles. If ROLE_ADMIN is true  all articles are shown
     */
    public function indexAction($category,$page)
    {
        $is_admin = $this->is_admin();
        $em = $this->getDoctrine()->getManager();

        /* - This parameters are sent from AJAX calls when Filter button is clicked*/
        $active=$this->getRequest()->query->get("active");
        $publish=$this->getRequest()->query->get("publish");

        $active=(null!== $active)?filter_var( $active, FILTER_VALIDATE_BOOLEAN):null;
        $publish = (null!==$publish)?filter_var($publish, FILTER_VALIDATE_BOOLEAN):null;
        $category=($category===null)?"all":$category;

        //count user's articles: if admin si logged in all articles are taken into consideration
        $user_id=$this->getUser()->getId();
        $articles_count=($is_admin)?($em->getRepository("JohnArticleBundle:Article")->countArticles($active,$publish,$category))
                                   :($em->getRepository("JohnArticleBundle:Article")->countArticles($active,$publish,$category,$user_id));

        /* - This query string is appended to the rewrite_url
           - for example  articles/mathematics/2?active=true&publish=true */

        $query_string=(isset($active) && isset($publish))?("?active=".var_export($active, true)."&publish=".var_export($publish, true)):null;
        $rewrite_url=$this->generateUrl("articles")."/".$category;
        $articles_per_page=$this->container->getParameter("articles_per_page");

        $pagerOptions=array(
                'items_per_page'=>$articles_per_page,
                'items_count'=>$articles_count,
                'class_name'=>'paginator',
                'rewrite_url'=>$rewrite_url,
                'query_string'=>$query_string,
                'current_page'=>$page
            );

        $pag=new Pagination($pagerOptions);
        list($from,$to)=$pag->getLimits();
        $pagination_links=$pag->getLinks();

        /* - for ROLE_ADMIN, $user_id=null, => all articles are fetched from the database  */
        $user_id=($is_admin)?null: $user_id;
        $articles=$em->getRepository("JohnArticleBundle:Article")->getArticles( $active,$publish,$category,$from,$articles_per_page, $user_id);

        /*Attach Form Views (Edit, Publish, Delete) to every article:*/
        foreach($articles as $article)
        {
            if(!$article->getPublished()){
                $id=$article->getId();
                $delete_form=$this->createDeleteForm($id);
                $publish_form=$this->createPublishForm($id,'Send to publish');
                $article->setDeleteForm($delete_form->createView());
                $article->setPublishForm($publish_form->createView());
            }
        }



        if($is_admin){
            /* - Create the form for the filter
               - The entity manager is sent in constructor to access slugNamePairs from Category repository.*/

            $filter_form=$this->createForm(new FilterType($em),null,array(
                'attr'=>array(
                    'id'=>'filter_form'
                )))->createView();
        }else{

            $filter_form=null;
        }

        return $this->render("JohnArticleBundle:Article:index.html.twig",array(
            'articles'=>$articles,
            'filter_form'=> $filter_form,
            'pagination_links'=>$pagination_links
        ));
    }

    /**
     * Insert the new article in the database
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $article= new Article();
        $article_form=$this->createCreateForm($article);
        $article_form->handleRequest($request);

        if($article_form->isValid())
        {
            /* - If Publish button was clicked, the article is set as published and will be active after the admin will accept it */
            if( $article_form->has("publish") &&  $article_form->get("publish")->isClicked()){
                $article->setPublished(true);
                $request->getSession()->getFlashBag()->add('publish_message','Congratulations! Your article has been sent for publish. You will receive an email after approval!');
            }
            $article->setAuthor($this->getUser());
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($article);
            $manager->flush();

            return $this->redirect($this->generateUrl('article_show',array(
                  'id'=>$article->getId(),
              )));

        }

        return $this->render('JohnArticleBundle:Article:new.html.twig', array(
            'entity'=>$article,
            'form'=>$article_form->createView()
        ));
    }


    /**
     * Display the new form
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function newAction()
    {

        $entity=new Article();
        $article_form=$this->createCreateForm($entity);

        return $this->render("JohnArticleBundle:Article:new.html.twig",array(
           'form'=>$article_form->createView()
        ));
    }

    /**
     * Finds and dysplays an article
     **/
    public function showAction($id,$entity=null)
    {
       $manager = $this->getDoctrine()->getManager();

       $entity = (is_null($entity))?$manager->getRepository('JohnArticleBundle:Article')->find($id):$entity;

        if(!$entity){
            throw $this->createNotFoundException("No article entity was found");
        }

        /*-----------Show some buttons for admin------------------------------------------------------------------------------------------*/
        if($this->is_admin()){
            /* - Create delete form*/
            $delete_form=$this->createDeleteForm($id);
            /* - Create publish form with specific submit's label (Publish or Unpublish) visibile for unpublish articles*/
            $is_published=$entity->getPublished();
            $publish_form = $this->createPublishForm($id, $is_published?"Unpublish":"Publish");

            /*--------Create Active form with button's label Activate or Deactivate-----------------------------------------*/
            /*--------Only activated articles are seen on the site----------------------------------------------------------*/
            $is_activated=$entity->getActive();
            $activate_form = $this->createActivateForm($id, $is_activated?"Set Invisibile":"Set Visibile");

            return $this->render('JohnArticleBundle:Article:show.html.twig',array(
                'entity'=>$entity,
                'delete_form'=>$delete_form->createView(),
                'publish_form'=>$publish_form->createView(),
                'activate_form'=>$activate_form->createView(),
                'published'=>$is_published
            ));
        }else{
            return $this->render('JohnArticleBundle:Article:show.html.twig',array(
                'entity'=>$entity,
            ));
        }

    }

    /**
     * Update an  article
     */
    public function updateAction(Request $request,$id)
    {
       $entity_manager = $this->getDoctrine()->getManager();
       $entity = $entity_manager->getRepository('JohnArticleBundle:Article')->find($id);

        /*----Forbidden for published articles when the user role is not ROLE_ADMIN------------------------------------*/
       if(!$entity || (!$this->is_admin() && $entity->getPublished())){
           return $this->createNotFoundException("Entity not found");
       }

       $form = $this->createEditForm($entity);
       $form->handleRequest($request);
       if($form->isValid())
       {
           $entity_manager->persist($entity);

           /*- If the article has a publish button , add a flash message if it was clicked*/
           if($form->has("publish") && $form->get("publish")->isClicked()){
               $entity->setPublished(true);
               $this->getRequest()->getSession()->getFlashBag()->add('publish_message','Congratulations! Your article has been sent for publish. You will receive an email after approval!');
           }

           $entity_manager->flush();

          return $this->redirect($this->generateUrl('article_show',array(
              'id'=>$id)
          ));

       }

        return $this->render('JohnArticleBundle:Article:edit.html.twig',array(
            'entity'=>$entity,
            'edit_form'=>$form->createView()
        ));
    }

    /**
     * Display the edit form for an article
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
         $entity_manager=$this->getDoctrine()->getManager();
         $entity = $entity_manager->getRepository('JohnArticleBundle:Article')->find($id);

        /*-----For USER_ROLE----------------------------------------------------------------------------------------*/
        /*-----Forbidden for published articles --------------------------------------------------------------------*/
        /*-----Articles sent to be published is not editable--------------------------------------------------------*/

        if(!$this->is_admin() && $entity->getPublished()){
            $this->getRequest()->getSession()->getFlashBag()->add('publish_message','Congratulations! Your article has been sent for publish. You will receive an email after approval!');
            return $this->redirect($this->generateUrl('article_show',array('id'=>$id)));
        }

        $edit_form = $this->createEditForm($entity);

        return $this->render("JohnArticleBundle:Article:edit.html.twig",array(
           'entity'=>$entity,
           'edit_form'=>$edit_form->createView(),
        ));
    }


    /**
     * Return the edit form
     * @param $entity
     * @return \Symfony\Component\Form\Form
     */
    public function createEditForm($entity)
    {
        return $this->createForm(new ArticleType($this->container->get('security.context')),$entity, array(
            'method'=>'POST',
            'action'=>$this->generateUrl('article_update',array(
                'id'=>$entity->getId()
            )),
            'attr'=>array(
                'class'=>'article-form'
            )
        ));
    }

    /**
     *Delete the article
     */
    public function deleteAction(Request $request, $id)
    {
        $delete_form=$this->createDeleteForm($id);
        $delete_form->handleRequest($request);

        if($delete_form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JohnArticleBundle:Article')->find($id);

            if(!$entity){
                throw $this->createNotFoundException("Unable to find  entity ");
            }

            /*---Articles sent to be published can be deleted only by admin-------------------*/

            if($entity->getPublished() && !$this->is_admin()){
                throw $this->createNotFoundException("Unable to find  entity");
            }
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('articles'));
    }

    /**
     * Create the form for the new article
     * @param $entity
     * @return \Symfony\Component\Form\Form
     */

    public function createCreateForm($entity)
    {
        return $this->createForm(new ArticleType($this->container->get('security.context')),$entity,array(
            'method'=>'POST',
            'action'=>$this->generateUrl(
                'article_create'
            ),
            'attr'=>array(
                'class'=>'article-form'
            )

        ));
    }

    /**
     * Get the delete form
     * @param int $id
     * @return \Symfony\Component\Form\Form
     */
    public function createDeleteForm($id)
    {
        /*return $this->createFormBuilder()
            ->setAction($this->generateUrl('article_delete',array('id'=>$id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label'=>'Delete this article'))
            ->getForm();*/
        /*------ Multiple forms on a page created  with FormBuilder are rendered with the same id : <form><div id="form">....</div></form>. To avoid this create form with NamedBuilder------*/
        return $this->container->get("form.factory")
                               ->createNamedBuilder("delete_form",'form')
                               ->setAction($this->generateUrl('article_delete',array(
                                   'id'=>$id
                               )))
                               ->setMethod("DELETE")
                               ->add('delete','submit',array(
                                   'attr'=>array(
                                       "onclick"=>"return confirm('Are you sure you want to delete this article?')"
                                   )))
                               ->getForm();
    }


    /**
     * create the published submit button
     * @param int $id
     * @param string $label
     * @return mixed
     */
    public function createPublishForm($id,$label)
    {
        /*------ Multiple forms on a page created  with FormBuilder are rendered with the same id : <form><div id="form">....</div></form>. To avoid this create form with NamedBuilder------*/
        return $this->container->get("form.factory")
                                ->createNamedBuilder('publish_form','form')
                                ->setMethod("POST")
                                ->setAction($this->generateUrl("article_publish",array(
                                    'id'=>$id,
                                    'action_label'=>$label
                                )))
                                ->add('submit','submit',array(
                                    'label'=>$label,
                                    'attr'=>array(
                                        "onclick"=>"return confirm('Are you sure you want to publish this article?')"
                                    )))
                                ->getForm();
    }


    /**
     * Publish or unpublish the article (article->published=false|true)
     * @param Request $request
     * @param string $id
     * @param string $action_label
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function publishAction(Request $request, $id, $action_label="publish")
    {
        $publish_form=$this->createPublishForm($id,$action_label);

        $publish_form->handleRequest($request);

        if($publish_form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $article=$em->getRepository("JohnArticleBundle:Article")->find($id);
           if(!$article){
               throw $this->createNotFoundException("Unable to find the entity");
           }
            if($article->getPublished()){
                $article->setPublished('false');
            }else{
                $article->setPublished(1);
            }
            $em->flush();
            $request->getSession()->getFlashBag()->add('publish_message',"Your {$action_label} action was done successfully. You will receive an email after approval");
        }
       return $this->redirect($this->generateUrl('articles'));
    }

    /**
     * Create the active submit button
     * @param int $id
     * @param string $label
     * @return \Symfony\Component\Form\Form
     */
    public function createActivateForm($id,$label)
    {
        return $this->createFormBuilder()
            ->setMethod("POST")
            ->setAction($this->generateUrl("article_activate",array(
                "id"=>$id,
                "btn_label"=>$label
            )))
            ->add('submit','submit',array(
                'label'=>$label,
                'attr'=>array(
                    "onclick"=>"return confirm('Are you sure you want to activate this article?')"
                )))
            ->getForm();
    }

    /**
     * This action activate an article (been active mean that the administrator aprove it to be published on the site)
     * @param Request $request
     * @param INT $id
     * @param string $btn_label
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function activateAction(Request $request,$id,$btn_label)
    {

        $activate_form=$this->createActivateForm($id,$btn_label);
        $activate_form->handleRequest($request);
        if($activate_form->isValid()){

            $em=$this->getDoctrine()->getManager();
            $article=$em->getRepository("JohnArticleBundle:Article")->find($id);
            if(!$article || !$this->is_admin()){
                throw $this->createNotFoundException("Entity nor found");
            }

            /*-IF ADMIN ACTIVATE AN ARTICLE THAT IS NOT PUBLISHED-*/
            if(!$article->getPublished() && !$article->getActive()){
                $article->setPublished(1);
            }

            if($article->getActive()){
                $article->setActive(0);
            }else{
                $article->setActive(1);

                /*--------Send notofication to article.activate listener-----------------------------------------/
                /---------This listener integrate PictureFill tags in article's body and send email to author---*/

                $this->get("event_dispatcher")->dispatch(ArticleEvents::ARTICLE_ACTIVATE,new ArticleEvent($article->getAuthor()->getEmail(),$article->getAuthor()->getFirstName(),$article));


            }
            $em->flush();
            $request->getSession()->getFlashBag()->add('publish_message',"Your {$btn_label} action was done successfully");
        }
        return $this->redirect($this->generateUrl('articles'));
    }

    /**
     * Check if the user has admin role
     * @return bool
     */
    public function is_admin()
    {
        return $this->container->get('security.context')->isGranted("ROLE_ADMIN");
    }
}