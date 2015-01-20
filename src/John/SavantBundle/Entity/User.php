<?php

namespace John\SavantBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="John\SavantBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields="email", message="Emailul a fost luat")
 */
class User implements AdvancedUserInterface,Serializable,EquatableInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    protected $first_name;




    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    protected $last_name;
    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    protected $email;

    /**
     * @var string
     */
    protected $plainPassword;

    /**
     * @param string $plain_password
     */
    public function setPlainPassword($plain_password)
    {
        $this->plainPassword = $plain_password;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $is_active=false;


    /**
     * @var array
     * @ORM\Column(type="array")
     */
    protected $roles=array();

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $salt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @ORM\ManyToMany(targetEntity="John\ArticleBundle\Entity\Category", inversedBy="subscribers")
     *
     */
    protected $interested_fields;

    /**
     * @var string
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    protected $profile;

    /**
     * @var string Store the existing path of the image
     */
    protected $temp;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="John\ArticleBundle\Entity\Article", mappedBy="author")
     */
    protected $articles;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="John\ArticleBundle\Entity\Comment", mappedBy="author")
     */
    protected $comments;


    public function __construct()
    {
        $this->salt= md5(uniqid(mt_rand(),true));
        $this->interested_fields=new ArrayCollection();
        $this->articles=new ArrayCollection();
        $this->comments=new ArrayCollection();
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
     * Set first_name
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    
        return $this;
    }

    /**
     * Get first_name
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
    
        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;
    
        return $this;
    }

    /**
     * Get is_active
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    
        return $this;
    }

    /**
     * Get roles
     *
     * @return array 
     */
    public function getRoles()
    {
        $roles=$this->roles;
        $roles[]="ROLE_USER";
        return array_unique($roles);
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
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
     * @ORM\PrePersist
     */

    public function setCreatedValue()
    {
        $this->created=new \DateTime();
    }

    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return Boolean true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return Boolean true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return Boolean true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return Boolean true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->getIsActive();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array(
           "id"=>$this->getId()
        ));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $data=unserialize($serialized);
        $this->id=$data['id'];

    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->setPlainPassword(null);
    }



    /**
     * Add interested_field
     *
     * @param \John\ArticleBundle\Entity\Category $interestedField
     * @return User
     */
    public function addInterestedField(\John\ArticleBundle\Entity\Category $interestedField)
    {
        $this->interested_fields[] = $interestedField;
    
        return $this;
    }

    /**
     * Remove interested_field
     *
     * @param \John\ArticleBundle\Entity\Category $interestedField
     */
    public function removeInterestedField(\John\ArticleBundle\Entity\Category $interestedField)
    {
        $this->interested_fields->removeElement($interestedField);
    }

    /**
     * Get interested_field
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInterestedFields()
    {
        return $this->interested_fields;
    }

    /**
     * Set profile
     *
     * @param string $profile
     * @return User
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    
        return $this;
    }

    /**
     * Get profile
     *
     * @return string 
     */
    public function getProfile()
    {
        return $this->profile;
    }

    public function isEqualTo(UserInterface $user)
    {
        return $this->getId() === $user->getId();
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
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        if(!empty($this->profile)){
            $this->temp=$this->profile;
            $this->profile=null;
        }else{
            $this->profile="initial";
        }
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
       return 'uploads/profile_images';
    }

    /**
     * @return string
     */
    public function getUploadRootDir()
    {
        return __DIR__."/../../../../web/".$this->getUploadDir();
    }

    /**
     * @return null|string
     */
    public function getWebPath()
    {
       return null===$this->profile
           ?null
           :$this->getUploadDir()."/".$this->profile;


    }

    /**
     * @return null|string
     */
    public function getAbsolutePath()
    {
       return null===$this->profile
           ?null
           :$this->getUploadRootDir().'/'.$this->getProfile();

    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
             //do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            //$this->profile = $filename.'.'.$this->getFile()->guessExtension();
           $ext =  $this->getFile()->guessExtension() == null
           ? $this->getFile()->getExtension()
           : $this->getFile()->guessExtension();
            $this->profile = $filename.'.'.$ext;
        }

    }


    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */
    public function upload()
    {
        if(null === $this->getFile()){
            return;
        }

        $this->getFile()->move($this->getUploadRootDir(),$this->profile);

        if(!empty($this->temp)){
            unlink($this->getUploadRootDir().'/'.$this->temp);
            $this->temp=null;
        }

        $this->file=null;
    }

    /**
     * @ORM\PostRemove
     */
    public function removeUpload()
    {
        if($file=$this->getAbsolutePath()){
            unlink($file);
        }
    }


    /**
     * Add articles
     *
     * @param \John\ArticleBundle\Entity\Article $articles
     * @return User
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
     * Add comments
     *
     * @param \John\ArticleBundle\Entity\Comment $comments
     * @return User
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
}