<?php
namespace John\UsersBundle\DataFixtures\ORM;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use John\UsersBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface,ContainerAwareInterface
{

    /**
     * {@inheritDoc}
     */
    private $container;

    public function load(ObjectManager $manager)
    {

        $user= new User();
        $user->setIsActive(true);
        $user->setEmail("john007buc@yahoo.com");
        $user->setFirstName("Johan");
        $user->setLastName("Johannes");
        $user->setRoles(array("ROLE_USER","ROLE_ADMIN"));
        $user->setPassword($this->encodePass($user,"1234"));
        $this->addReference('admin',$user);
        $manager->persist($user);
        $manager->flush();

        $user2= new User();
        $user2->setIsActive(true);
        $user2->setEmail("punctul_rosu@yahoo.com");
        $user2->setFirstName("Johan");
        $user2->setLastName("Johannes");
        $user2->setRoles(array("ROLE_USER"));
        $user2->setPassword($this->encodePass($user2,"1234"));
        $this->addReference('user',$user);
        $manager->persist($user2);
        $manager->flush();


    }


    private function encodePass(User $user, $plain_pass)
    {
        /*$factory=$this->container->get("security.encoder_factory");
        $encoder=$factory->getEncoder($user);

        return $encoder->encodePassword($plain_pass, $user->getSalt());*/
        $encoder = $this->container->get('security.encoder_factory')
            ->getEncoder($user);

        return $encoder->encodePassword($plain_pass, $user->getSalt());
    }

    public function setContainer(ContainerInterface $container=null)
    {
      $this->container=$container;
    }

    public function getOrder()
    {
        return 2;
    }

}