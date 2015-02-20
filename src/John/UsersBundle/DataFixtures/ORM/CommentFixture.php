<?php
namespace John\UsersBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use John\ArticleBundle\Entity\Comment;

class LoadCommentData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager )
    {
        $comment1 = new Comment();
        $comment1->setContent("LOrem ipsumLOrem ipsumLOrem ipsumLOrem ipsumLOrem ipsumLOrem ipsum");
        $comment1->setAuthor($this->getReference("admin"));
        $comment1->setArticle($this->getReference('article1'));

        $manager->persist($comment1);
        $manager->flush();

    }

    public function getOrder()
    {
        return 5;
    }
}