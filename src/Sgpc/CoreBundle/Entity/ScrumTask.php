<?php

namespace Sgpc\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Entity(repositoryClass="Sgpc\CoreBundle\Repository\TaskRepository")
 */
class ScrumTask extends Task {

    /**
     * @ORM\ManyToOne(targetEntity="Sprint", inversedBy="tasks")
     * @ORM\JoinColumn(name="sprint_id", referencedColumnName="id")
     */
    protected $sprint;

    /**
     *
     * @var int
     * @ORM\Column(name="hours", type="integer")
     */
    protected $hours;

    /**
     * @var string
     *
     * @ORM\Column(name="lastlisting", type="string", length=100, nullable=true)
     */
    protected $lastListing;

    /**
     * @var int
     *
     * @ORM\Column(name="finished", type="boolean")
     */
    protected $finished;

    public function __construct() {
        parent::__construct();

        $this->finished = false;
    }

    /**
     * Set hours
     *
     * @param integer $hours
     *
     * @return ScrumTask
     */
    public function setHours($hours) {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return integer
     */
    public function getHours() {
        return $this->hours;
    }

    /**
     * Set sprint
     *
     * @param \Sgpc\CoreBundle\Entity\Sprint $sprint
     *
     * @return ScrumTask
     */
    public function setSprint(\Sgpc\CoreBundle\Entity\Sprint $sprint = null) {
        $this->sprint = $sprint;

        return $this;
    }

    /**
     * Get sprint
     *
     * @return \Sgpc\CoreBundle\Entity\Sprint
     */
    public function getSprint() {
        return $this->sprint;
    }

    /**
     * Set lastListing
     *
     * @param string $lastListing
     *
     * @return ScrumTask
     */
    public function setLastListing($lastListing) {
        $this->lastListing = $lastListing;

        return $this;
    }

    /**
     * Get lastListing
     *
     * @return string
     */
    public function getLastListing() {
        return $this->lastListing;
    }

    /**
     * Set finished
     *
     * @param boolean $finished
     *
     * @return ScrumTask
     */
    public function setFinished($finished) {
        $this->finished = $finished;

        return $this;
    }

    /**
     * Get finished
     *
     * @return boolean
     */
    public function getFinished() {
        return $this->finished;
    }

}
