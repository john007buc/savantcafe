<?php
namespace John\ArticleBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use John\UsersBundle\Entity\User;
use John\ArticleBundle\Entity\Article;

class ArticleEvent extends Event
{
    /**
     * @var User
     */
    protected $authorEmail;

    protected $authorName;

    /**
     * @var Article
     */
    protected $article;

    /**
     * @param string Email
     * @param Article $article
     */
    public function __construct($email, $name, Article $article)
    {
     $this->authorEmail=$email;
     $this->authorName = $name;
     $this->article=$article;
    }


    /**
     * @return User
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    public function getAuthorName()
    {
        return $this->authorName;
    }

}