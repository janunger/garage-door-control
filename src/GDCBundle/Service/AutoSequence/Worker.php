<?php

namespace GDCBundle\Service\AutoSequence;

use GDCBundle\Event\AutoSequenceStartedEvent;
use GDCBundle\Event\CommandIssuedEvent;
use GDCBundle\Model\AutoSequenceName;
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
        $this->activeSequence = $this->factory->createSequenceFor($event->getCommand());
        $this->eventDispatcher->dispatch(
            'gdc.autosequence_started',
            new AutoSequenceStartedEvent($this->activeSequence->getName())
        );
    }

    public function tick()
    {
        if (null === $this->activeSequence) {
            return;
        }

        $state = $this->activeSequence->tick();
        if ($state->equals(State::FINISHED())) {
            $this->activeSequence = null;
        }
    }
}