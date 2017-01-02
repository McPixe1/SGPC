<?php

namespace Sgpc\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Worklog
 *
 * @ORM\Table(name="worklog")
 * @ORM\Entity(repositoryClass="Sgpc\CoreBundle\Repository\WorklogRepository")
 */
class Worklog
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
     * @ORM\Column(name="workedHours", type="integer")
     */
    private $workedHours;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @ORM\ManyToOne(targetEntity="ScrumTask", inversedBy="worklog")
     * @ORM\JoinColumn(name="scrumtask_id", referencedColumnName="id")
     */
    private $task;


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
     * Set workedHours
     *
     * @param integer $workedHours
     *
     * @return Worklog
     */
    public function setWorkedHours($workedHours)
    {
        $this->workedHours = $workedHours;

        return $this;
    }

    /**
     * Get workedHours
     *
     * @return int
     */
    public function getWorkedHours()
    {
        return $this->workedHours;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Worklog
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set task
     *
     * @param \Sgpc\CoreBundle\Entity\ScrumTask $task
     *
     * @return Worklog
     */
    public function setTask(\Sgpc\CoreBundle\Entity\ScrumTask $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \Sgpc\CoreBundle\Entity\ScrumTask
     */
    public function getTask()
    {
        return $this->task;
    }
}
