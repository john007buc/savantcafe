<?php
namespace John\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Class Category
 * @package John\SavantBundle\Entity
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="John\ArticleBundle\Entity\CategoryRepository")
 *
 */
class Category
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

     /**
      * @Gedmo\Slug(fields={"name"}, updatable=false)
      * @ORM\Column(type="string", length=130)
    */
    protected $slug;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="John\UsersBundle\Entity\User", mappedBy="$interested_fields")
     */
    protected $subscribers;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Article", mappedBy="categories")
     */
    protected $articles;

    public function __construct()
    {
        $this->subscribers=new ArrayCollection();
        $this->articles=new ArrayCollection();
    }




    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add subscribers
     *
     * @param \John\UsersBundle\Entity\User $subscribers
     * @return Category
     */
    public function addSubscriber(\John\UsersBundle\Entity\User $subscribers)
    {
        $this->subscribers[] = $subscribers;
    
        return $this;
    }

    /**
     * Remove subscribers
     *
     * @param \John\UsersBundle\Entity\User $subscribers
     */
    public function removeSubscriber(\John\UsersBundle\Entity\User $subscribers)
    {
        $this->subscribers->removeElement($subscribers);
    }

    /**
     * Get subscribers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Add articles
     *
     * @param \John\ArticleBundle\Entity\Article $articles
     * @return Category
     */
    public function addArticle(\John\ArticleBundle\Entity\Article $articles)
    {
        $this->articles[] = $articles;
    
        return $this;
    }

    /**
     * Remove articles
     *
     * @param \John\ArticleBundle\Entity\Article $articles
     */
    public function removeArticle(\John\ArticleBundle\Entity\Article $articles)
    {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
}