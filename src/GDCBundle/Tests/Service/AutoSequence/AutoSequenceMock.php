<?php

namespace GDCBundle\Tests\Service\AutoSequence;

use GDCBundle\Model\AutoSequenceName;
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

    /**
     * @var AutoSequenceName
     */
    private $name;

    public function __construct(AutoSequenceName $name = null)
    {
        $this->state = State::RUNNING();
        if (null === $name) {
            $name = new AutoSequenceName('autosequence-mock');
        }
        $this->name = $name;
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

    /**
     * @return AutoSequenceName
     */
    public function getName()
    {
        return $this->name;
    }
}