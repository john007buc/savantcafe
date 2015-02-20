<?php
namespace John\UsersBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use John\ArticleBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
       $physics = new Category;
       $physics->setName("Physics");
       $this->addReference("physics",$physics);
       $manager->persist($physics);

        $astronomy = new Category;
        $astronomy->setName("Astronomy");
        $this->addReference("astronomy",$astronomy);
        $manager->persist($astronomy);

        $mathematics = new Category;
        $mathematics->setName("Mathematics");
        $this->addReference("mathematics",$mathematics);
        $manager->persist($mathematics);

        $chemistry = new Category;
        $chemistry->setName("Chemistry");
        $this->addReference("Chemistry",$chemistry);
        $manager->persist($chemistry);

        $it = new Category;
        $it->setName("IT");
        $this->addReference("it",$it);
        $manager->persist($it);

        $manager->flush();

    }

    public function getOrder()
    {
        return 1;
    }
}