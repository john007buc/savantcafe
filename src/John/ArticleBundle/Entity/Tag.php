<?php
namespace John\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Tag
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity()
 */
class Tag
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
     * @ORM\Column(type="string", length=60)
     */
    protected $name;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Article", mappedBy="tags")
     */
    protected $articles;

    public function __construct()
    {
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
     * @return Tag
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
     * Add articles
     *
     * @param \John\ArticleBundle\Entity\Article $articles
     * @return Tag
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

    public function __toString()
    {
        return $this->getName();
    }
}