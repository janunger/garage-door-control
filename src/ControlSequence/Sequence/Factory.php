<?php

declare(strict_types=1);

namespace JUIT\GDC\ControlSequence\Sequence;

use JUIT\GDC\ControlSequence\Command;
use JUIT\GDC\Door\DoorInterface;

class Factory
{
    /**
     * @var DoorInterface
     */
    private $door;

    public function __construct(DoorInterface $door)
    {
        $this->door = $door;
    }

    public function createSequenceFor(Command $command): Sequence
    {
        return new TriggerDoor($this->door);
    }
}
