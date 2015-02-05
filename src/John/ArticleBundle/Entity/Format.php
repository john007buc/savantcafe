<?php
namespace John\ArticleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Format
 * @package John\ArticleBundle\Entity
 * @ORM\Table(name="formats")
 * @ORM\Entity()
 */
class Format
{

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /**
     * @var "John\ArticleBundle\Entity\Format;"
     * @ORM\OneToMany(targetEntity="Media", mappedBy="type")
     *
     */
    protected $media;

    public function __construct()
    {
        $this->media=new ArrayCollection();
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
     * @return Format
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
     * Add media
     *
     * @param \John\ArticleBundle\Entity\Media $media
     * @return Format
     */
    public function addMedia(\John\ArticleBundle\Entity\Media $media)
    {
        $this->media[] = $media;
    
        return $this;
    }

    /**
     * Remove media
     *
     * @param \John\ArticleBundle\Entity\Media $media
     */
    public function removeMedia(\John\ArticleBundle\Entity\Media $media)
    {
        $this->media->removeElement($media);
    }

    /**
     * Get media
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedia()
    {
        return $this->media;
    }
}