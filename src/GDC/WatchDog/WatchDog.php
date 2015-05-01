<?php

namespace GDC\WatchDog;

use GDC\Door\HardwareErrorException;
use GDC\Door\DoorInterface;
use GDC\Door\State;
use GDCBundle\Entity\DoorState;
use GDCBundle\Entity\DoorStateRepository;
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
     * @var Messenger
     */
    private $messenger;

    /**
     * @var DoorStateRepository
     */
    private $doorStateRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        DoorInterface $door,
        Messenger $messenger,
        DoorStateRepository $doorStateRepository,
        EventDispatcherInterface $dispatcher
    ) {
        $this->door                = $door;
        $this->messenger           = $messenger;
        $this->doorStateRepository = $doorStateRepository;
        $this->dispatcher          = $dispatcher;
    }

    public function execute()
    {
        try {
            $currentState = $this->door->getState();
        } catch (HardwareErrorException $e) {
            $this->messenger->sendHardwareError();

            return;
        }

        $this->updateDoorState($currentState);

        $previousState = $this->state;
        if ($currentState->equals($previousState)) {
            return;
        }

        $this->state = $currentState;

        if (null === $previousState) {
            $this->messenger->sendMessageOnWatchdogRestart();
        } elseif ($previousState->equals(State::CLOSED())) {
            $this->messenger->sendMessageOnDoorOpening();
        } elseif ($previousState->equals(State::UNKNOWN()) && $currentState->equals(State::CLOSED())) {
            $this->messenger->sendMessageAfterDoorClosed();
        }
    }

    private function updateDoorState(State $currentState)
    {
        $stateEntity = $this->doorStateRepository->find(1);
        if (!$stateEntity) {
            $stateEntity = new DoorState();
        }
        $stateEntity->setState($currentState);
        $stateEntity->setDate(new \DateTime());
        $this->doorStateRepository->save($stateEntity);
    }
}
