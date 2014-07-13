<?php

namespace GDC\WatchDog;

use GDC\Door;

class WatchDog
{
    /**
     * @var Door
     */
    private $door;

    /**
     * @var string|null
     */
    private $state = null;

    /**
     * @var Messenger
     */
    private $messenger;

    public function __construct(Door $door, Messenger $messenger)
    {
        $this->door = $door;
        $this->messenger = $messenger;
    }

    public function execute()
    {
        try {
            $currentState = $this->door->getState();
        } catch (Door\HardwareErrorException $e) {
            $this->messenger->sendHardwareError();
            return;
        }

        $previousState = $this->state;
        if ($currentState === $previousState) {
            return;
        }

        if (null === $previousState) {
            $this->messenger->sendMessageOnWatchdogRestart();
        } elseif (Door::STATE_CLOSED === $previousState) {
            $this->messenger->sendMessageOnDoorOpening();
        } elseif (Door::STATE_UNKNOWN === $previousState && Door::STATE_CLOSED === $currentState) {
            $this->messenger->sendMessageAfterDoorClosed();
        }

        $this->state = $currentState;
    }
}
