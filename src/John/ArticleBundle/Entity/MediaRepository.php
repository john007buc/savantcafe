<?php

namespace John\ArticleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MediaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MediaRepository extends EntityRepository
{

    public function getAuthorImages($user_id)
    {

        $query = $this->getEntityManager()->createQuery("select m.path from JohnArticleBundle:Media m join m.author u join m.type f where u.id=:user_id and f.name='image' ")
                                           ->setParameter('user_id',$user_id);


       // dump($query->getResult());exit();
        //get an array of images
        $images=array_map(function ($v){
            return $v["path"];
        },$query->getResult());

        return $images;

    }
}
