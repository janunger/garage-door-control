<?php

namespace GDCBundle\Service\AutoSequence;

use GDCBundle\Event\CommandIssuedEvent;

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

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function onCommandIssued(CommandIssuedEvent $event)
    {
        $this->activeSequence = $this->factory->createSequenceFor($event->getCommand());
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