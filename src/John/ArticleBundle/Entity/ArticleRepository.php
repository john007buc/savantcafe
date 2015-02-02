<?php

namespace John\ArticleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends EntityRepository
{

    public function countArticles($active,$published,$category=null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a)');

        if($category){
            $qb->join('a.categories','c')
                ->where('c.slug=:category')
                ->setParameter('category',$category);
        }

        if($active && $published)
        {
            $qb->andWhere('a.published=:published')
                ->setParameter('published',$published)
                ->andWhere('a.active=:active')
                ->setParameter('active',$active);
        }


        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getArticles($active,$published,$category=null,$offset=null,$max=null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a');


        if($category){
            $qb->join('a.categories','c')
                ->where('c.slug=:category')
                ->setParameter('category',$category);
        }




        if(!is_null($active)){
            $qb->andWhere('a.active=:active')
                ->setParameter('active',$active);
        }

        if(!is_null($published)){

                 $qb->andWhere('a.published=:published')
                  ->setParameter('published',$published);
        }




        if($offset){
            $qb->setFirstResult($offset);
        }

        if($max){
            $qb->setMaxResults($max);
        }

        return $qb->getQuery()->getResult();
    }
}
