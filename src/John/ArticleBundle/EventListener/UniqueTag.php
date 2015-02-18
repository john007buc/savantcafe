<?php

namespace John\ArticleBundle\EventListener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use John\ArticleBundle\Entity\Article;
use John\ArticleBundle\Entity\Tag;
class UniqueTag
{

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity=$args->getEntity();
        $manager = $args->getEntityManager();

        if($entity instanceof Article)
        {
              $tags = $entity->getTags();
              foreach( $tags as $key=>$tag){

                //CHANGE NAME TO LOWER CASE
                 $tag->setName(strtolower($tag->getName()));

                 $result = $manager->getRepository('JohnArticleBundle:Tag')->findOneBy(array('name'=>$tag->getName()));

                  if($result){

                      //for avoid duplicated entry we first verify if the tag wasn't already added
                      if(!$tags->contains($result))
                      {
                          $tags[$key]=$result;
                         // $tags->removeElement($tag);
                      }else{
                          //if the tag has been added we remove the item from array collection
                         unset($tags[$key]);
                      }

                 }
              }


           // add existings tags selected from dropdown list
           $this->add_existing_tags($entity);

        }

    }

    public function add_existing_tags($entity)
    {
        if($e_tags=$entity->existing_tags)
        {
            $tags = $entity->getTags();
            foreach($e_tags as $existing_tag)
            {
                if(!$tags->contains($existing_tag)){
                    $tags->add($existing_tag);
                }
            }
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $manager = $args->getEntityManager();


        if($entity instanceof Article) {

            $tags = $entity->getTags();

            foreach ($tags as $tag) {

                $tag->setName(strtolower($tag->getName()));

                /*In preUpdate events, the newest tags are persisted to Doctrine even if there is no flush()
                  So if a tag exist, it is persisted end the bellow query will find it twice.
                  Because the order is ASC, the new added tag wil be the last in the result (it has a greater id)*/

                $result = $manager->getRepository('JohnArticleBundle:Tag')->findBy(array('name' => $tag->getName()), array('id' => 'ASC'));

                /*If there are duplicate tags*/
                if (count($result) > 1) {


                    /*Remove the new added tag from entity and entity manager(because we don't want to add it again)*/
                    $entity->removeTag($result[1]);
                    $manager->remove($result[1]);


                    /*Add the existing tag to the entity if the entity doesn't contain it yet */
                    /* in the case of duplicate tags: ex mathematica and mathematica*/
                    /*At first iteration the second mathematica object from result is removed and the first one is added*/
                    /*At second iteration the same thing happen so we must avoid do add again the first object to the entity: primary key error in article_tag table*/

                    if (!$entity->getTags()->contains($result[0])) {
                        $entity->addTag($result[0]);
                    }

                }
            }

            $this->add_existing_tags($entity);
        }elseif($entity instanceof Tag){

            //IF ONLIY TAG NAME WAS UPDATED, CHANGE NAME TO LOWER CASE
            $N=strtolower($entity->getName());
            $entity->setName($N);
           // dump($entity);exit();
        }






        }




}