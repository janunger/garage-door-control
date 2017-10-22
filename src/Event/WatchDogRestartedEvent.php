<?php

declare(strict_types=1);

namespace JUIT\GDC\Event;

use JUIT\GDC\Door\State;
use Symfony\Component\EventDispatcher\Event;

class WatchDogRestartedEvent extends Event
{
    /**
     * @var State
     */
    private $doorState;

    public function __construct(State $doorState)
    {
        $this->doorState = $doorState;
    }

    public function getDoorState(): State
    {
        return $this->doorState;
    }
}
