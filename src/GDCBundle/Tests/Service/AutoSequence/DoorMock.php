<?php

namespace GDCBundle\Tests\Service\AutoSequence;

use GDC\Door\DoorInterface;
use GDC\Door\HardwareErrorException;
use GDC\Door\State;

class DoorMock implements DoorInterface
{
    private $state;

    private $triggerControlCount = 0;

    public function __construct()
    {
        $this->state = State::CLOSED();
    }

    /**
     * @return State
     * @throws HardwareErrorException
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return void
     */
    public function triggerControl()
    {
        $this->triggerControlCount++;
    }

    /**
     * @param State $state
     */
    public function setState(State $state)
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getTriggerControlCount()
    {
        return $this->triggerControlCount;
    }
}