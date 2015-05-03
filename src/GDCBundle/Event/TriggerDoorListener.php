<?php

namespace GDCBundle\Event;

use GDC\CommandQueue\Command;
use GDC\Door\DoorInterface;

class TriggerDoorListener
{
    /**
     * @var DoorInterface
     */
    private $door;

    public function __construct(DoorInterface $door)
    {
        $this->door = $door;
    }

    public function onCommandIssued(CommandIssuedEvent $event)
    {
        if (!$event->getCommand()->equals(Command::TRIGGER_DOOR())) {
            return;
        }

        $this->door->triggerControl();
    }
}
