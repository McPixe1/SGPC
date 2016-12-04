<?php

namespace Sgpc\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity(repositoryClass="Sgpc\CoreBundle\Repository\TaskRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Task {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Listing", inversedBy="tasks")
     * @ORM\JoinColumn(name="listing_id", referencedColumnName="id")
     */
    protected $listing;
    
    
    
     /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="tasks")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;
    
    /**
     * @var int
     *
     * @ORM\Column(name="isactive", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="tasks", cascade={"persist"})
     * @ORM\JoinTable(name="task_user",
     *     joinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     *
     * @var ArrayCollection
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="task", cascade={"remove"})
     */
    protected $comments;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="due_date", type="datetime")
     */
    protected $dueDate;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Task
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Task
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set listing
     *
     * @param \Sgpc\CoreBundle\Entity\Listing $listing
     * @return Task
     */
    public function setListing(\Sgpc\CoreBundle\Entity\Listing $listing = null) {
        $this->listing = $listing;

        return $this;
    }

    /**
     * Get listing
     *
     * @return \Sgpc\CoreBundle\Entity\Listing 
     */
    public function getListing() {
        return $this->listing;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Task
     */
    public function setPriority($priority) {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority() {
        return $this->priority;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isActive = true;
    }

    /**
     * Add users
     *
     * @param \Sgpc\CoreBundle\Entity\User $users
     * @return Task
     */
    public function addUser(\Sgpc\CoreBundle\Entity\User $users) {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Sgpc\CoreBundle\Entity\User $users
     */
    public function removeUser(\Sgpc\CoreBundle\Entity\User $users) {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @ORM\PrePersist
     */
    public function setCreatedAt() {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @ORM\PreUpdate
     */
    public function setUpdatedAt() {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     *
     * @return Task
     */
    public function setDueDate(\DateTime $dueDate) {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime
     */
    public function getDueDate() {
        return $this->dueDate;
    }

    /**
     * Add comment
     *
     * @param \Sgpc\CoreBundle\Entity\Comment $comment
     *
     * @return Task
     */
    public function addComment(\Sgpc\CoreBundle\Entity\Comment $comment) {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \Sgpc\CoreBundle\Entity\Comment $comment
     */
    public function removeComment(\Sgpc\CoreBundle\Entity\Comment $comment) {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments() {
        return $this->comments;
    }

    public function __toString() {
    return $this->name;
}

    /**
     * Set project
     *
     * @param \Sgpc\CoreBundle\Entity\Project $project
     *
     * @return Task
     */
    public function setProject(\Sgpc\CoreBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Sgpc\CoreBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Task
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
