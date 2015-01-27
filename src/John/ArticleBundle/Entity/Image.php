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


    protected $temp;


    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     *
     *
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
     * @var UploadedFile
     */
    private $file;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $alt;

    public function getAbsolutePath()
    {
        return null===$this->name
            ? null
            : $this->getUploadRootDir()."/".$this->name;
    }

    public function getWebPath()
    {
        return null===$this->name
            ? null
            : $this->getUploadDir()."/".$this->name;
    }

    public function getUploadRootDir()
    {
        return __DIR__."/../../../../web/".$this->getUploadDir();
    }

    public function getUploadDir()
    {
        return "uploads/featured_images";
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


    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file=$file;
        if(isset($this->name)){
            $this->temp = $this->name;
            $this->name=null;
            $this->updated=new \DateTime();
        }else{
            $this->name='initial';
        }

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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpload()
    {
        if(null!=$this->getFile()) {

            $hash = sha1(uniqid(mt_rand(), true));
            $this->name = $hash .".". $this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */
    public function upload()
    {
       if($this->getFile()===null){
           return;
       }

        $this->getFile()->move($this->getUploadRootDir(),$this->name);

        if(isset($this->temp) && is_file($this->getUploadRootDir()."/".$this->temp)){
            $this->temp=null;
            @chmod($this->getUploadRootDir(), 0777 );

            @unlink($this->getUploadRootDir()."/".$this->temp);

        }else{
            $this->file=null;
        }

    }

    /**
     * @ORM\PreRemove
     */
    public function removeUpload()
    {
       if(is_file($this->getAbsolutePath())){
           @chmod($this->getUploadRootDir(), 0777 );
           @unlink($this->getAbsolutePath());

       }
    }




    /**
     * Set alt
     *
     * @param string $alt
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    
        return $this;
    }

    /**
     * Get alt
     *
     * @return string 
     */
    public function getAlt()
    {
        return $this->alt;
    }
}