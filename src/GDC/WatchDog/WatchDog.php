<?php

namespace GDC\WatchDog;

use GDC\Door\HardwareErrorException;
use GDC\Door\DoorInterface;
use GDC\Door\State;

class WatchDog
{
    /**
     * @var DoorInterface
     */
    private $door;

    /**
     * @var State|null
     */
    private $state = null;

    /**
     * @var Messenger
     */
    private $messenger;

    public function __construct(DoorInterface $door, Messenger $messenger)
    {
        $this->door = $door;
        $this->messenger = $messenger;
    }

    public function execute()
    {
        try {
            $currentState = $this->door->getState();
        } catch (HardwareErrorException $e) {
            $this->messenger->sendHardwareError();
            return;
        }

        $previousState = $this->state;
        if ($currentState->equals($previousState)) {
            return;
        }

        if (null === $previousState) {
            $this->messenger->sendMessageOnWatchdogRestart();
        } elseif ($previousState->equals(State::CLOSED())) {
            $this->messenger->sendMessageOnDoorOpening();
        } elseif ($previousState->equals(State::UNKNOWN()) && $currentState->equals(State::CLOSED())) {
            $this->messenger->sendMessageAfterDoorClosed();
        }

        $this->state = $currentState;
    }
}
