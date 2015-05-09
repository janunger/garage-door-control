<?php

namespace GDCBundle\Service\AutoSequence;

use GDCBundle\Event\AutoSequenceStartedEvent;
use GDCBundle\Event\CommandIssuedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Worker
{
    /**
     * @var AutoSequence|null
     */
    private $activeSequence = null;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(Factory $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory         = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function onCommandIssued(CommandIssuedEvent $event)
    {
        $newSequence = $this->factory->createSequenceFor($event->getCommand());
        if (null !== $newSequence) {
            $this->startSequence($newSequence);
        } else {
            $this->terminateCurrentSequence();
        }
    }

    public function tick()
    {
        if (null === $this->activeSequence) {
            return;
        }

        $state = $this->activeSequence->tick();
        if ($state->equals(State::FINISHED())) {
            $this->terminateCurrentSequence();
        }
    }

    private function terminateCurrentSequence()
    {
        $this->activeSequence = null;
        $this->eventDispatcher->dispatch('gdc.auto_sequence_terminated');
    }

    /**
     * @param $newSequence
     */
    private function startSequence(AutoSequence $newSequence)
    {
        $this->activeSequence = $newSequence;
        $this->eventDispatcher->dispatch(
            'gdc.auto_sequence_started',
            new AutoSequenceStartedEvent($this->activeSequence->getName())
        );
    }
}