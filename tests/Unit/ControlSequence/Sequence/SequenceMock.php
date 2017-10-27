<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit\ControlSequence\Sequence;

use JUIT\GDC\ControlSequence\Sequence\Sequence;
use JUIT\GDC\ControlSequence\Sequence\State;

class SequenceMock implements Sequence
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

    public function tick(): State
    {
        $this->receivedTicks++;

        return $this->state;
    }

    public function getReceivedTicks(): int
    {
        return $this->receivedTicks;
    }

    public function setState(State $state)
    {
        $this->state = $state;
    }
}
