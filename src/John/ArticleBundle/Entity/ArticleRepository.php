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

    public function countArticles($published=1,$active=1,$category_id=null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a)')
            ->where('a.published=:published')
            ->setParameter('published',$published)
            ->andWhere('active=:active')
            ->setParameter('active',$active);

        if($category_id){
            $qb->join('a.categories','c')
                ->andWhere('c.category_id=:category_id')
                ->setParameter('category_id',$category_id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getArticles($published=1,$active=1,$offset=null,$max=null,$category_id=null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.published=:published')
            ->setParameter('published',$published)
            ->andWhere('a.active=a:active')
            ->setParameter('active',$active);

        if($category_id){
            $qb->join('a.categories','c')
                ->andWhere('c.category_id=:category_id')
                ->setParameter('category_id',$category_id);
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
