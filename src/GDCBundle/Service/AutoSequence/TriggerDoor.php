<?php

namespace GDCBundle\Service\AutoSequence;

use GDC\Door\DoorInterface;
use GDCBundle\Model\AutoSequenceName;

class TriggerDoor implements AutoSequence
{
    const NAME = 'trigger-door';

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

    /**
     * @return AutoSequenceName
     */
    public function getName()
    {
        return new AutoSequenceName(self::NAME);
    }
}