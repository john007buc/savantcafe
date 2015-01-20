<?php
namespace John\SavantBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use John\SavantBundle\Entity\Article;

class LoadArticleData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $article1 = new Article();
        $article1->addCategory($this->getReference('astronomy'));
        $article1->setTitle("Star blue");
        $article1->setSlug("star-blue");
        $article1->setUrl("www.savantcafe.com");
        $article1->setAuthor($this->getReference("admin"));
        $article1->setContent("blue starblue starblue starblue starblue starblue star");
        $article1->addTag($this->getReference("tag1"));
        $this->addReference("article1",$article1);
        $manager->persist($article1);
        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }




}