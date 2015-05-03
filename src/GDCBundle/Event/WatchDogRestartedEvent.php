<?php

namespace GDCBundle\Event;

use GDC\Door\State;
use Symfony\Component\EventDispatcher\Event;

class WatchDogRestartedEvent extends Event
{
    /**
     * @var State
     */
    private $currentState;

    public function __construct(State $currentState)
    {
        $this->currentState = $currentState;
    }

    /**
     * @return State
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }
}