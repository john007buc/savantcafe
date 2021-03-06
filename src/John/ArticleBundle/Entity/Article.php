<?php
namespace John\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Class Article
 * @package John\ArticleBundle\Entity
 * @ORM\Table(name="articles")
 * @ORM\Entity(repositoryClass="John\ArticleBundle\Entity\ArticleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Article
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $title;

    /**
     * @var string
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @ORM\Column(type="string", length=130)
     */
    protected $slug;

    /**
     * @var strig
     *
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @var string
     * @ORM\Column(type="string", length=150)
     */
    protected $url;

    /**
     * @var string
     * @ORM\Column(type="text", length=2000)
     */
    protected $abstract;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="articles")
     */
    protected $categories;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="article")
     */
    protected $comments;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="articles", cascade={"persist","remove"})
     */
    protected $tags;

    /**
     * @ORM\ManyToOne(targetEntity="John\UsersBundle\Entity\User", inversedBy="articles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $author;

    /**
     * @ORM\OneToOne(targetEntity="Image", inversedBy="article", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true)
     */
    protected $featured_image;


    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $active=false;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $published=false;


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

    public $existing_tags=array();

    protected $publish_form;

    protected $edit_form;

    protected $delete_form;


    public function __construct()
    {
        $this->categories=new ArrayCollection();
        $this->comments=new ArrayCollection();
        $this->tags=new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Article
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

    /**
     * Set content
     *
     * @param string $content
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Article
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Add categories
     *
     * @param \John\ArticleBundle\Entity\Category $categories
     * @return Article
     */
    public function addCategory(\John\ArticleBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;
    
        return $this;
    }

    /**
     * Remove categories
     *
     * @param \John\ArticleBundle\Entity\Category $categories
     */
    public function removeCategory(\John\ArticleBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add comments
     *
     * @param \John\ArticleBundle\Entity\Comment $comments
     * @return Article
     */
    public function addComment(\John\ArticleBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \John\ArticleBundle\Entity\Comment $comments
     */
    public function removeComment(\John\ArticleBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add tags
     *
     * @param \John\ArticleBundle\Entity\Tag $tags
     * @return Article
     */
    public function addTag(\John\ArticleBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
    
        return $this;
    }

    /**
     * Remove tags
     *
     * @param \John\ArticleBundle\Entity\Tag $tags
     */
    public function removeTag(\John\ArticleBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set author
     *
     * @param \John\UsersBundle\Entity\User $author
     * @return Article
     */
    public function setAuthor(\John\UsersBundle\Entity\User $author = null)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return \John\UsersBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Article
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
     * @return Article
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
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created=new \DateTime();
        $this->updated=new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
        $this->updated=new \DateTime();
    }


    /**
     * Set featured_image
     *
     * @param \John\ArticleBundle\Entity\Image $featuredImage
     * @return Article
     */
    public function setFeaturedImage(\John\ArticleBundle\Entity\Image $featuredImage = null)
    {
        $this->featured_image = $featuredImage;
    
        return $this;
    }

    /**
     * Get featured_image
     *
     * @return \John\ArticleBundle\Entity\Image 
     */
    public function getFeaturedImage()
    {
        return $this->featured_image;
    }



    /**
     * Set active
     *
     * @param boolean $active
     * @return Article
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Article
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Add categories
     *
     * @param \John\ArticleBundle\Entity\Category $categories
     * @return Article
     */
    public function addCategorie(\John\ArticleBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;
    
        return $this;
    }

    /**
     * Remove categories
     *
     * @param \John\ArticleBundle\Entity\Category $categories
     */
    public function removeCategorie(\John\ArticleBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    public function areTagsValid(ExecutionContextInterface $context)
    {
        if(count($this->getTags())){
            $i=0;
            foreach($this->getTags() as $tag)
            {

                if(!preg_match("/^[a-zA-Z ]*$/",$tag->getName()))
                {
                    $context->addViolationAt("tags[$i].name", 'Only letters and white space are allowed', array(), null);
                }
                $i++;

            }

        }
    }




    public function setDeleteForm(FormView $delete_form)
    {
        $this->delete_form=$delete_form;
    }

    public function getDeleteForm()
    {
        return $this->delete_form;
    }

    public function setPublishForm(FormView $publish_form)
    {
        $this->publish_form=$publish_form;
    }

    public function getPublishForm()
    {
        return $this->publish_form;
    }

    public function setEditForm(FormView $edit_form)
    {
        $this->edit_form=$edit_form;

    }

    public function getEditForm()
    {
        return $this->edit_form;
    }

    /**
     * Set abstract
     *
     * @param string $abstract
     * @return Article
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    
        return $this;
    }

    /**
     * Get abstract
     *
     * @return string 
     */
    public function getAbstract()
    {
        return $this->abstract;
    }
}