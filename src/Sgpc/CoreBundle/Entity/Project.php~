<?php

namespace Sgpc\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="Sgpc\CoreBundle\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\OneToMany(targetEntity="Listing", mappedBy="project",  cascade={"remove"})
     */
    protected $listings;
    

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="projects", cascade={"persist"})
     * @ORM\JoinTable(name="project_user",
     *     joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     *
     * @var ArrayCollection
     */
    protected $users;
    
    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    public function __toString()
    {
        return $this->getName();
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
     * Set users
     *
     * @param array $users
     * @return Project
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return array 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Project
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
     * Set description
     *
     * @param string $description
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->listings = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add users
     *
     * @param \Sgpc\CoreBundle\Entity\User $users
     * @return Project
     */
    public function addUser(\Sgpc\CoreBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Sgpc\CoreBundle\Entity\User $users
     */
    public function removeUser(\Sgpc\CoreBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Add listings
     *
     * @param \Sgpc\CoreBundle\Entity\Listing $listings
     * @return Project
     */
    public function addListing(\Sgpc\CoreBundle\Entity\Listing $listings)
    {
        $this->listings[] = $listings;

        return $this;
    }

    /**
     * Remove listings
     *
     * @param \Sgpc\CoreBundle\Entity\Listing $listings
     */
    public function removeListing(\Sgpc\CoreBundle\Entity\Listing $listings)
    {
        $this->listings->removeElement($listings);
    }

    /**
     * Get listings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getListings()
    {
        return $this->listings;
    }

    /**
     * Set owner
     *
     * @param \Sgpc\CoreBundle\Entity\User $owner
     * @return Project
     */
    public function setOwner(\Sgpc\CoreBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Sgpc\CoreBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
