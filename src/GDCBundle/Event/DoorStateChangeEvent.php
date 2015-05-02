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
     * @var State
     */
    private $previousState;

    /**
     * @var Microtime
     */
    private $time;

    public function __construct(State $newState, State $previousState, Microtime $time)
    {
        $this->newState = $newState;
        $this->time = $time;
        $this->previousState = $previousState;
    }

    /**
     * @return State
     */
    public function getNewState()
    {
        return $this->newState;
    }


    /**
     * @return State
     */
    public function getPreviousState()
    {
        return $this->previousState;
    }

    /**
     * @return Microtime
     */
    public function getTime()
    {
        return $this->time;
    }
}