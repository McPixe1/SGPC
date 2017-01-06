<?php

namespace Sgpc\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Task
 *
 * @ORM\Entity(repositoryClass="Sgpc\CoreBundle\Repository\TaskRepository")
 */
class KanbanTask extends Task {

       /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\Type("\DateTime")
     * @ORM\Column(name="due_date", type="datetime")
     */
    protected $dueDate;
    

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     *
     * @return KanbanTask
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }
}
