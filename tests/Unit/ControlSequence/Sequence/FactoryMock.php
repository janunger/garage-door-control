<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit\ControlSequence\Sequence;

use JUIT\GDC\ControlSequence\Command;
use JUIT\GDC\ControlSequence\Sequence\FactoryInterface;
use JUIT\GDC\ControlSequence\Sequence\Sequence;

class FactoryMock implements FactoryInterface
{
    /**
     * @var Command[]
     */
    private $receivedCommands = [];

    /**
     * @var Sequence[]
     */
    private $sequencesToReturn = [];

    public function createSequenceFor(Command $command): Sequence
    {
        $this->receivedCommands[] = $command;
        $at = count($this->receivedCommands) - 1;

        return $this->sequencesToReturn[$at];
    }

    /**
     * @return Command[]
     */
    public function getReceivedCommands(): array
    {
        return $this->receivedCommands;
    }

    /**
     * @param Sequence[] $sequencesToReturn
     */
    public function setSequencesToReturn(array $sequencesToReturn)
    {
        $this->sequencesToReturn = $sequencesToReturn;
    }
}
