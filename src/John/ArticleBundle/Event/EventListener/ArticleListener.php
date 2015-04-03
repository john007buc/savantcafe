<?php
namespace John\ArticleBundle\Event\EventListener;

use Doctrine\ORM\EntityManager;
use John\ArticleBundle\Event\ArticleEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;


class ArticleListener
{
    protected $container;
    protected $authorEmail;
    protected $authorName;
    protected $article;
    protected $em;

    public function __construct(Objectmanager $em,ContainerInterface $container){
        $this->container=$container;
        $this->em=$em;
    }

    public function setPictureFillTags(ArticleEvent $articleEvent)
    {

        $this->authorEmail=$articleEvent->getAuthorEmail();
        $this->authorName=$articleEvent->getAuthorName();
        $this->article=$articleEvent->getArticle();

        $this->article->setContent("Modified in article listener");

        $this->em->persist($this->article);
        $this->em->flush();
    }

    public function sendAuthorEmail()
    {
        if( $this->authorEmail){

            $message = \Swift_Message::newInstance()
                ->setSubject('SavantCafe publishing')
                ->setFrom('admin@savantcafe.com')
                ->setTo('john007buc@yahoo.com')
                ->setBody(
                    $this->container->get('templating')->render("JohnArticleBundle::email.html.twig",array(
                        'author'=>$this->authorName,
                        'article'=>$this->article
                    )),'text/html'
                );

            $this->container->get("mailer")->send($message);
        }
    }




}