<?php

namespace GDC\WatchDog;

use GDC\Door\HardwareErrorException;
use GDC\Door\DoorInterface;
use GDC\Door\State;
use GDCBundle\Event\WatchDogEvents;
use GDCBundle\Event\WatchDogRestartedEvent;
use GDCBundle\Model\Microtime;
use GDCBundle\Service\DoorStateWriter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var DoorStateWriter
     */
    private $doorStateWriter;

    public function __construct(
        DoorInterface $door,
        Messenger $messenger,
        DoorStateWriter $doorStateWriter,
        EventDispatcherInterface $dispatcher
    ) {
        $this->door            = $door;
        $this->doorStateWriter = $doorStateWriter;
        $this->dispatcher      = $dispatcher;

        $this->init();
    }

    private function init()
    {
        try {
            $this->state = $this->door->getState();
            $this->dispatcher->dispatch(WatchDogEvents::RESTARTED, new WatchDogRestartedEvent($this->state));
        } catch (HardwareErrorException $e) {
            $this->dispatcher->dispatch(WatchDogEvents::HARDWARE_ERROR);
        }
    }

    public function execute()
    {
        try {
            $currentState = $this->door->getState();
        } catch (HardwareErrorException $e) {
            $this->dispatcher->dispatch(WatchDogEvents::HARDWARE_ERROR);

            return;
        }

        $this->doorStateWriter->write($currentState, new Microtime());

        $previousState = $this->state;
        if ($currentState->equals($previousState)) {
            return;
        }

        $this->state = $currentState;

        if ($previousState->equals(State::CLOSED())) {
            $this->dispatcher->dispatch(WatchDogEvents::DOOR_OPENING);
        } elseif ($previousState->equals(State::UNKNOWN()) && $currentState->equals(State::CLOSED())) {
            $this->dispatcher->dispatch(WatchDogEvents::DOOR_CLOSED);
        }
    }
}
