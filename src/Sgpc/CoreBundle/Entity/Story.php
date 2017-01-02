<?php

namespace Sgpc\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Story
 *
 * @ORM\Table(name="story")
 * @ORM\Entity(repositoryClass="Sgpc\CoreBundle\Repository\StoryRepository")
 */
class Story
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
     * @ORM\ManyToOne(targetEntity="Sprint", inversedBy="stories")
     * @ORM\JoinColumn(name="sprint_id", referencedColumnName="id", nullable=true)
     */
    private $sprint;
    
    /**
     * @ORM\OneToMany(targetEntity="ScrumTask", mappedBy="story", cascade={"remove"})
     */
    private $tasks;

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
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set sprint
     *
     * @param \Sgpc\CoreBundle\Entity\Sprint $sprint
     *
     * @return Story
     */
    public function setSprint(\Sgpc\CoreBundle\Entity\Sprint $sprint = null)
    {
        $this->sprint = $sprint;

        return $this;
    }

    /**
     * Get sprint
     *
     * @return \Sgpc\CoreBundle\Entity\Sprint
     */
    public function getSprint()
    {
        return $this->sprint;
    }

    /**
     * Add task
     *
     * @param \Sgpc\CoreBundle\Entity\Task $task
     *
     * @return Story
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
}
