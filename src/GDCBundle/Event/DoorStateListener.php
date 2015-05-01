<?php

namespace GDCBundle\Event;

use GDCBundle\Service\DoorStateWriter;

class DoorStateListener
{
    /**
     * @var DoorStateWriter
     */
    private $doorStateWriter;

    public function __construct(DoorStateWriter $doorStateWriter)
    {
        $this->doorStateWriter = $doorStateWriter;
    }

    public function onDoorStateChange(DoorStateChangeEvent $event)
    {
        $this->doorStateWriter->write($event->getNewState(), $event->getTime());
    }
}