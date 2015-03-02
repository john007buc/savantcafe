<?php
namespace John\ArticleBundle\Controller;

use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use John\ArticleBundle\Form\ArticleType;
use John\ArticleBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use John\ArticleBundle\Form\FilterType;
use John\ArticleBundle\MyClasses\Pagination;


class ArticleController extends Controller
{

    /*
     * Show user's article. If ROLE_ADMIN is true  all articles are shown
     */
    public function indexAction($category,$page)
    {

        $em = $this->getDoctrine()->getManager();

        /* - This parameters are sent via AJAX calls when Filter button is clicked*/
        $active=$this->getRequest()->query->get("active");
        $publish=$this->getRequest()->query->get("publish");

        $active=(null!== $active)?filter_var( $active, FILTER_VALIDATE_BOOLEAN):null;
        $publish = (null!==$publish)?filter_var($publish, FILTER_VALIDATE_BOOLEAN):null;
        $category=($category===null)?"all":$category;

        //count user's articles: if admin si logged in all articles are taken into consideration
        $user_id=$this->getUser()->getId();
        $articles_count=($this->container->get("security.context")->isGranted("ROLE_ADMIN"))?($em->getRepository("JohnArticleBundle:Article")->countArticles($active,$publish,$category))
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

        /* - for ROLE_ADMIN, $user_id=null, so all articles are fetched from the database  */
        $user_id=($this->container->get("security.context")->isGranted("ROLE_ADMIN"))?null: $user_id;
        $articles=$em->getRepository("JohnArticleBundle:Article")->getArticles( $active,$publish,$category,$from,$articles_per_page, $user_id);

        /* - Create the form for the filter
           - The entity manager is sent in constructor to access slugNamePairs from Category repository.*/

        $filter_form=$this->createForm(new FilterType($em),null,array(
            'attr'=>array(
                'id'=>'filter_form'
            )));

        return $this->render("JohnArticleBundle:Article:index.html.twig",array(
            'articles'=>$articles,
            'filter_form'=>$filter_form->createView(),
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
            if($publish=$article_form->get("publish")->isClicked()){
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
    public function showAction($id)
    {
       $manager = $this->getDoctrine()->getManager();
       $entity = $manager->getRepository('JohnArticleBundle:Article')->find($id);

        if(!$entity){
            throw $this->createNotFoundException("No article entity was found");
        }
        /* - Create delete form*/
        $delete_form=$this->createDeleteForm($id);
        /* - Create publish form with specific submit's label (Publish or Unpublish) visibile for unpublish articles and for ROLE_ADMIN*/
        $is_published=$entity->getPublished();
        $publish_form = $this->createPublishForm($id, $is_published?"Unpublish":"Publish");

        return $this->render('JohnArticleBundle:Article:show.html.twig',array(
            'entity'=>$entity,
            'delete_form'=>$delete_form->createView(),
            'publish_form'=>$publish_form->createView(),
            'published'=>$is_published
        ));
    }

    /**
     * Update an  article
     */
    public function updateAction(Request $request,$id)
    {
       $entity_manager = $this->getDoctrine()->getManager();
       $entity = $entity_manager->getRepository('JohnArticleBundle:Article')->find($id);
       if(!$entity){
           return $this->createNotFoundException("Entity not found");
       }

       $form = $this->createEditForm($entity);
       $form->handleRequest($request);
       if($form->isValid())
       {
           $entity_manager->persist($entity);

           /*- Set as published and add a flash message if the Publish button was clicked*/
           if($form->get("publish")->isClicked()){
               $entity->setPublished(true);
               $entity_manager->flush();
               /* - Create a flash message*/
               $this->getRequest()->getSession()->getFlashBag()->add('publish_message','Congratulations! Your article has been sent for publish. You will receive an email after approval!');

               return $this->redirect($this->generateUrl('article_show',array('id'=>$id)));

           }else{
               $entity_manager->flush();
               return $this->redirect($this->generateUrl('article_show',array('id'=>$id)));
           }
       }

        $delete_form=$this->createDeleteForm($id);
        return $this->render('JohnArticleBundle:Article:edit.html.twig',array(
            'entity'=>$entity,
            'edit_form'=>$form->createView(),
            'delete_form'=>$delete_form->createView()
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

        if(!$this->container->get("security.context")->isGranted("ROLE_ADMIN") && $entity->getPublished()){

            $this->getRequest()->getSession()->getFlashBag()->add('publish_message','Congratulations! Your article has been sent for publish. You will receive an email after approval!');
            return $this->redirect($this->generateUrl('article_show',array('id'=>$id)));
            //return $this->redirect($this->generateUrl("articles"));
        }

        $edit_form = $this->createEditForm($entity);
        $delete_form=$this->createDeleteForm($id);

        return $this->render("JohnArticleBundle:Article:edit.html.twig",array(
           'entity'=>$entity,
           'edit_form'=>$edit_form->createView(),
            'delete_form'=>$delete_form->createView()
        ));
    }


    /**
     * Return the edit form
     * @param $entity
     * @return \Symfony\Component\Form\Form
     */
    public function createEditForm($entity)
    {
        return $this->createForm(new ArticleType(),$entity, array(
            'method'=>'POST',
            'action'=>$this->generateUrl('article_update',array('id'=>$entity->getId())),
            'attr'=>array('class'=>'article-form')
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
                throw $this->createNotFoundException("Unable to find article entity with id:{$id}");
            }

            $em->remove($entity);
           // dump( $entity);exit();
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
        return $this->createForm(new ArticleType(),$entity,array(
            'method'=>'POST',
            'action'=>$this->generateUrl('article_create'),
            'attr'=>array('class'=>'article-form')
        ));
    }

    /**
     * Get the delete form
     * @param $id
     * @return \Symfony\Component\Form\Form
     */
    public function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('article_delete',array('id'=>$id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label'=>'Delete this article'))
            ->getForm();
    }

    public function createPublishForm($id,$label)
    {
        return $this->createFormBuilder()
            ->setMethod("POST")
            ->setAction($this->generateUrl("article_publish",array('id'=>$id,'action_label'=>$label)))
            ->add('submit','submit',array('label'=>$label))
            ->getForm();

    }

    public function publishAction(Request $request, $id, $action_label="publish")
    {
        $publish_form=$this->createPublishForm($id,$action_label);

        $publish_form->handleRequest($request);

        if($publish_form->isValid())
        {
            $em=$this->getDoctrine()->getManager();

            $article=$em->getRepository("JohnArticleBundle:Article")->find($id);


           if(!$article){
               return $this->createNotFoundException("Unable to find the entity");
           }

            //$article->setPublished('true');
            if($article->getPublished()){
                $article->setPublished('false');
            }else{
                $article->setPublished(1);
            }

           // dump($article);exit();
           // $em->persist($article);
            $em->flush();
            
            //de adaugat flash messages

        }
        return $this->redirect($this->generateUrl('articles'));
    }


}