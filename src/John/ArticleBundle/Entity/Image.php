<?php
namespace John\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Image
 * @package John\ArticleBundle\Entity
 * @ORM\Table(name="Images")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class Image
{


    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $root_dir;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\OneToOne(targetEntity="Article", mappedBy="featured_image")
     */
    protected $article;




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
     * Set root_dir
     *
     * @param string $rootDir
     * @return Image
     */
    public function setRootDir($rootDir)
    {
        $this->root_dir = $rootDir;
    
        return $this;
    }

    /**
     * Get root_dir
     *
     * @return string 
     */
    public function getRootDir()
    {
        return $this->root_dir;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Image
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


    private $file;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file)
    {
        $this->file=$file;

    }
    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Image
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Image
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set article
     *
     * @param \John\ArticleBundle\Entity\Article $article
     * @return Image
     */
    public function setArticle(\John\ArticleBundle\Entity\Article $article = null)
    {
        $this->article = $article;
    
        return $this;
    }

    /**
     * Get article
     *
     * @return \John\ArticleBundle\Entity\Article 
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
        $this->updated=new \DateTime();
    }
}