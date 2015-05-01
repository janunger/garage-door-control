<?php

namespace GDCBundle\Event;

use GDC\Door\State;
use GDCBundle\Model\Microtime;
use Symfony\Component\EventDispatcher\Event;

class DoorStateChangeEvent extends Event
{
    /**
     * @var State
     */
    private $newState;
    /**
     * @var Microtime
     */
    private $time;

    public function __construct(State $newState, Microtime $time)
    {
        $this->newState = $newState;
        $this->time = $time;
    }

    /**
     * @return State
     */
    public function getNewState()
    {
        return $this->newState;
    }

    /**
     * @return Microtime
     */
    public function getTime()
    {
        return $this->time;
    }
}