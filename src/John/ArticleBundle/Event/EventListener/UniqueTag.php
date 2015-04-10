<?php

namespace John\ArticleBundle\Event\EventListener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use John\ArticleBundle\Entity\Article;
use John\ArticleBundle\Entity\Tag;
use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;

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
        if($entity instanceof Article && (count($existing_tags=$entity->existing_tags)))
        {
            $tags = $entity->getTags();
            foreach($existing_tags as $existing_tag)
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
        $em = $args->getEntityManager();

        if($entity instanceof Article) {

            $tags = $entity->getTags();

            foreach ($tags as $tag) {

                /*In preUpdate events, the newest tags are persisted to Doctrine even if there is no flush()
                  So if a tag exist, it is persisted end the bellow query will find it twice.
                  Because the order is ASC, the newest tag wil be the last in the result (it has a greater id)*/

                /*-----------------------------------!!!!!!!!!!!--------------------------------------------------
                   This work if the database colletion is case insensitive: CHARSET=utf8 COLLATE=utf8_general_ci
                   For case sensitive the coolletion is:CHARSET=utf8 COLLATE=utf8_bin
                ---------------------------------------------------------------------------------------------------*/

                $result = $em->getRepository('JohnArticleBundle:Tag')->findBy(array('name' => $tag->getName()), array('id' => 'ASC'));
               // dump($result);

                /*If there are many identically tags added, remove them from entity and entity manager*/
                if (($counts=count($result)) > 1) {

                    /*Remove duplicates tags from entity and entity manager. Remain only the first tag*/
                   for($i=1;$i<$counts;$i++){

                       if($entity->getTags()->contains($result[$i]))
                       {
                           $entity->removeTag($result[$i]);

                           $em->remove($result[$i]);
                       };
                   }

                    /*Add the existing tag to the entity if the entity doesn't contain it yet */
                    /* in the case of duplicate tags. Ex: user enters  mathematica and mathematica*/
                    /*At first iteration the second mathematica object from result is removed and the first one is added*/
                    /*At second iteration the same thing happen so we must avoid do add again the first object to the entity: primary key error in article_tag table*/

                    if (!$entity->getTags()->contains($result[0])) {
                        $entity->addTag($result[0]);
                    }
                }else{
                    //IF THE TAG DOESN'T EXISTS, JUST RECONSIDER THE NEW LOWERCASE TAG
                    $tag->setName(strtolower($tag->getName()));
                    $classMetadata = $em->getClassMetadata(get_class($tag));
                    $uw=$em->getUnitOfWork();
                    $uw->computeChangeSet( $classMetadata,$tag);
                }
            }
            //this is for test
            

        }
        if($entity instanceof Tag){
            //IF ONLIY TAG NAME WAS UPDATED, CHANGE NAME TO LOWER CASE
            $entity->setName(strtolower($entity->getName()));
            $classMetadata = $em->getClassMetadata(get_class($entity));
            $em->getUnitOfWork()->recomputeSingleEntityChangeSet($classMetadata, $entity);
        }
        $this->add_existing_tags($entity);
    }
}