<?php
namespace John\SavantBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use John\SavantBundle\Entity\Tag;

class LoadTagData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
       $tag1 = new Tag();
        $tag1->setName("astronomie si astrologie");
       $this->addReference("tag1",$tag1);
        $manager->persist($tag1);
        $manager->flush();

    }

    public function getOrder()
    {
        return 3;
    }




}