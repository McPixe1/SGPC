<?php

namespace Sgpc\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sprint
 *
 * @ORM\Table(name="sprint")
 * @ORM\Entity(repositoryClass="Sgpc\CoreBundle\Repository\SprintRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Sprint
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
     * @var int
     *
     * @ORM\Column(name="isactive", type="boolean")
     */
    private $isActive;
    
     /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="sprints")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;
    
    
    /**
     * @ORM\OneToMany(targetEntity="Listing", mappedBy="sprint",  cascade={"remove"})
     */
    protected $listings;
    
     /**
     * @ORM\OneToMany(targetEntity="ScrumTask", mappedBy="sprint", cascade={"remove"})
     */
    protected $tasks;
    

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime", nullable=true)
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime", nullable=true)
     */
    private $end;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Sprint
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
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Sprint
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start = new \DateTime();
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return Sprint
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set project
     *
     * @param \Sgpc\CoreBundle\Entity\Project $project
     *
     * @return Sprint
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
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isActive = true;
    }


    
    /**
     * Add task
     *
     * @param \Sgpc\CoreBundle\Entity\Task $task
     *
     * @return Sprint
     */
    public function addTask(\Sgpc\CoreBundle\Entity\Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \Sgpc\CoreBundle\Entity\Task $task
     */
    public function removeTask(\Sgpc\CoreBundle\Entity\Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Add listing
     *
     * @param \Sgpc\CoreBundle\Entity\Listing $listing
     *
     * @return Sprint
     */
    public function addListing(\Sgpc\CoreBundle\Entity\Listing $listing)
    {
        $this->listings[] = $listing;

        return $this;
    }

    /**
     * Remove listing
     *
     * @param \Sgpc\CoreBundle\Entity\Listing $listing
     */
    public function removeListing(\Sgpc\CoreBundle\Entity\Listing $listing)
    {
        $this->listings->removeElement($listing);
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Sprint
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
