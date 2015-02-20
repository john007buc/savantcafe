<?php
namespace John\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Media
 * @package John\ArticleBundle\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass = "John\ArticleBundle\Entity\MediaRepository")
 * @ORM\HasLifecycleCallbacks
 *
 */
class Media
{

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var STRING
     * @ORM\Column(type="string", length=100)
     */
    protected $path;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $root_path;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="John\UsersBundle\Entity\User", inversedBy="media")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $author;

    /**
     * @ORM\ManyToOne(targetEntity="Format", inversedBy="media", cascade={"all"})
     * @ORM\JoinColumn(name="format_id", referencedColumnName="id")
     */
    protected $type;



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
     * Set path
     *
     * @param string $path
     * @return Media
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Media
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
     * Set author
     *
     * @param \John\UsersBundle\Entity\User $author
     * @return Media
     */
    public function setAuthor(\John\UsersBundle\Entity\User $author = null)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return \John\SavantBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set type
     *
     * @param \John\ArticleBundle\Entity\Format $type
     * @return Media
     */
    public function setType(\John\ArticleBundle\Entity\Format $type = null)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \John\ArticleBundle\Entity\Format 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */

    public function setUpdatedValue()
    {
        $this->updated=new \DateTime();
    }

    /**
     * Set root_path
     *
     * @param string $rootPath
     * @return Media
     */
    public function setRootPath($rootPath)
    {
        $this->root_path = $rootPath;
    
        return $this;
    }

    /**
     * Get root_path
     *
     * @return string 
     */
    public function getRootPath()
    {
        return $this->root_path;
    }
}