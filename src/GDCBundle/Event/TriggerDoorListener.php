<?php

namespace GDCBundle\Event;

use GDC\CommandQueue\Command;
use GDC\Door;

class TriggerDoorListener
{
    /**
     * @var Door
     */
    private $door;

    public function __construct(Door $door)
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
