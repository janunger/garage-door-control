<?php

namespace GDCBundle\Tests\Service\AutoSequence;

use GDC\CommandQueue\Command;
use GDCBundle\Service\AutoSequence\AutoSequence;
use GDCBundle\Service\AutoSequence\Factory;

class FactoryMock extends Factory
{
    /**
     * @var Command[]
     */
    private $receivedCommands = [];

    /**
     * @var AutoSequence[]
     */
    private $sequencesToReturn = [];

    public function createSequenceFor(Command $command)
    {
        $this->receivedCommands[] = $command;
        $at = count($this->receivedCommands) - 1;

        return $this->sequencesToReturn[$at];
    }

    /**
     * @return \GDC\CommandQueue\Command[]
     */
    public function getReceivedCommands()
    {
        return $this->receivedCommands;
    }

    /**
     * @param AutoSequence[] $sequencesToReturn
     */
    public function setSequencesToReturn(array $sequencesToReturn)
    {
        $this->sequencesToReturn = $sequencesToReturn;
    }
}