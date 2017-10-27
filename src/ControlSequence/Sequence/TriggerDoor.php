<?php

declare(strict_types=1);

namespace JUIT\GDC\ControlSequence\Sequence;

use JUIT\GDC\Door\DoorInterface;

class TriggerDoor implements Sequence
{
    /**
     * @var DoorInterface
     */
    private $door;

    public function __construct(DoorInterface $door)
    {
        $this->door = $door;
    }

    public function tick(): State
    {
        $this->door->triggerControl();

        return State::FINISHED();
    }
}
