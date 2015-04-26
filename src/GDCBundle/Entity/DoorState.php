<?php

namespace GDCBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use GDC\Door\State;

/**
 * @ORM\Entity(repositoryClass="GDCBundle\Entity\DoorStateRepository")
 * @ORM\Table(name="door_state", options={"engine"="memory"})
 */
class DoorState
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string")
     */
    private $state;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @return State
     */
    public function getState()
    {
        return new State($this->state);
    }

    /**
     * @param State $state
     */
    public function setState(State $state)
    {
        $this->state = $state->getValue();
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }
}
