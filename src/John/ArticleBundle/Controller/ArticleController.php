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

    public function indexAction($category,$page)
    {


        $em = $this->getDoctrine()->getManager();
        //$articles=$em->getRepository("JohnArticleBundle:Article")->getArticles(0);

            $active=(null!==$this->getRequest()->query->get("active"))?filter_var($this->getRequest()->query->get("active"), FILTER_VALIDATE_BOOLEAN):null;

            $publish = (null!==$this->getRequest()->query->get("publish"))?filter_var($this->getRequest()->query->get("publish"), FILTER_VALIDATE_BOOLEAN):null;

            $category=($category===null)?"all":$category;

            $articles_count =$em->getRepository("JohnArticleBundle:Article")->countArticles($active,$publish,$category);




        $query_string=(isset($active) && isset($publish))?("?active=".var_export($active, true)."&publish=".var_export($publish, true)):null;
        $rewrite_url=($category)?$this->generateUrl("articles")."/".$category:$this->generateUrl("articles")."/".$category;


            $pagerOptions=array(
                'items_per_page'=>1,
                'items_count'=>$articles_count,
                'class_name'=>'paginator',
                'rewrite_url'=>$rewrite_url,
                'query_string'=>$query_string,
                'current_page'=>$page
            );


      // dump();
            $pag=new Pagination($pagerOptions);
            list($from,$to)=$pag->getLimits();
            $pagination_links=$pag->getLinks();


            $articles=$em->getRepository("JohnArticleBundle:Article")->getArticles( $active,$publish,$category,$from,$pagerOptions['items_per_page']);


            //dump($articles);exit();
        //$nr=$em->getRepository("JohnArticleBundle:Article")->countArticles(1,0,2);
        //dump($nr);

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
     * Insert the new article in data base
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

            $article->setAuthor($this->getUser());
            $manager=$this->getDoctrine()->getManager();

            //$existing_tags=$article_form->get('existing_tags')->getData();

           // $new_tags = array_map('strtolower',$article->getTags()->toArray());

            //foreach($existing_tags as $e_tag){
                  //  $article->addTag($e_tag);
          // }

            $manager->persist($article);
            $manager->flush();

              return $this->redirect($this->generateUrl('article_show',array('id'=>$article->getId())));

        }

        return $this->render('JohnArticleBundle:Article:new.html.twig', array(
            'entity'=>$article,
            'form'=>$article_form->createView()
        ));
        //exit(Doctrine\Common\Util\Debug::dump($article_form->get('existing_tags')->getData()));

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
           'entity'=>$entity,
           'form'=>$article_form->createView()
        ));
    }

    /**
     * Finds and dysplays an article
     **/
    public function showAction($id)
    {
         dump("ghhfhfghfg");
       $manager = $this->getDoctrine()->getManager();
       $entity = $manager->getRepository('JohnArticleBundle:Article')->find($id);


        if(!$entity){
            return $this->createNotFoundException("No article entity was found");
        }

        $delete_form=$this->createDeleteForm($id);

        $publish_form = $this->createPublishForm($id,$entity->getPublished()?"Unpublish":"Publish");


        return $this->render('JohnArticleBundle:Article:show.html.twig',array(
           'entity'=>$entity,
            'delete_form'=>$delete_form->createView(),
            'publish_form'=>$publish_form->createView()
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

           //add existing tags from dropdown list  if the tag doesn't exist
           foreach($entity->existing_tags as $tag)
           {
               if(!$entity->getTags()->contains($tag)){
                   $entity->addTag($tag);
               }

           }

           $entity_manager->persist($entity);
           $entity_manager->flush();

           return $this->redirect($this->generateUrl('article_show',array('id'=>$id)));
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

                return $this->createNotFoundException("Unable to find article entity with id:{$id}");

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