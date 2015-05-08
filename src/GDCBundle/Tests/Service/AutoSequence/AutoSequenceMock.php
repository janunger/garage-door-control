<?php

namespace GDCBundle\Tests\Service\AutoSequence;

use GDCBundle\Service\AutoSequence\AutoSequence;
use GDCBundle\Service\AutoSequence\State;

class AutoSequenceMock implements AutoSequence
{
    /**
     * @var int
     */
    private $receivedTicks = 0;

    /**
     * @var State
     */
    private $state;

    public function __construct()
    {
        $this->state = State::RUNNING();
    }

    /**
     * @return State
     */
    public function tick()
    {
        $this->receivedTicks++;

        return $this->state;
    }

    /**
     * @return int
     */
    public function getReceivedTicks()
    {
        return $this->receivedTicks;
    }

    /**
     * @param State $state
     */
    public function setState(State $state)
    {
        $this->state = $state;
    }
}