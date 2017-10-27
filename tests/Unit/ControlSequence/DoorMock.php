<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit\ControlSequence;

use JUIT\GDC\Door\DoorInterface;
use JUIT\GDC\Door\State;

class DoorMock implements DoorInterface
{
    /** @var State */
    private $state;

    /** @var int */
    private $triggerControlCount = 0;

    public function getState(): State
    {
        return $this->state;
    }

    public function triggerControl()
    {
        $this->triggerControlCount++;
    }

    public function setState(State $state)
    {
        $this->state = $state;
    }

    public function getTriggerControlCount(): int
    {
        return $this->triggerControlCount;
    }
}
