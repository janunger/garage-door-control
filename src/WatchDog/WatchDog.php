<?php

declare(strict_types=1);

namespace JUIT\GDC\WatchDog;

use JUIT\GDC\Door\Door;
use JUIT\GDC\Door\State;
use JUIT\GDC\Event\WatchDogEvents;
use JUIT\GDC\Event\WatchDogRestartedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WatchDog
{
    /**
     * @var Door
     */
    private $door;

    /**
     * @var Messenger
     */
    private $messenger;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /** @var State */
    private $state;

    public function __construct(Door $door, Messenger $messenger, EventDispatcherInterface $eventDispatcher)
    {
        $this->door            = $door;
        $this->messenger       = $messenger;
        $this->eventDispatcher = $eventDispatcher;

        $this->init();
    }

    private function init()
    {
        $this->state = $this->door->getState();
        $this->eventDispatcher->dispatch(WatchDogEvents::RESTARTED, new WatchDogRestartedEvent($this->state));
    }

    public function execute()
    {
        $currentState = $this->door->getState();
        $previousState = $this->state;

        if ($currentState->equals($previousState)) {
            return;
        }

        $this->state = $currentState;
        if ($currentState->equals(State::HARDWARE_ERROR())) {
            $this->eventDispatcher->dispatch(WatchDogEvents::HARDWARE_ERROR);
        } elseif ($previousState->equals(State::CLOSED())) {
            $this->eventDispatcher->dispatch(WatchDogEvents::DOOR_OPENING);
        } elseif ($previousState->equals(State::UNKNOWN()) && $currentState->equals(State::CLOSED())) {
            $this->eventDispatcher->dispatch(WatchDogEvents::DOOR_CLOSED);
        }
    }
}
