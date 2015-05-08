<?php

namespace GDCBundle\Service\AutoSequence;

use GDC\Door\DoorInterface;

class TriggerDoor implements AutoSequence
{
    /**
     * @var DoorInterface
     */
    private $door;

    public function __construct(DoorInterface $door)
    {
        $this->door = $door;
    }

    /**
     * @return State
     */
    public function tick()
    {
        $this->door->triggerControl();

        return State::FINISHED();
    }
}