<?php

declare(strict_types=1);

namespace JUIT\GDC\ControlSequence;

use JUIT\GDC\ControlSequence\Sequence\FactoryInterface;
use JUIT\GDC\ControlSequence\Sequence\Sequence;
use JUIT\GDC\ControlSequence\Sequence\State;
use JUIT\GDC\Event\CommandIssuedEvent;

class Worker
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /** @var Sequence */
    private $sequence;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function onCommandIssued(CommandIssuedEvent $event)
    {
        $this->sequence = $this->factory->createSequenceFor($event->getCommand());
    }

    public function tick()
    {
        if (null === $this->sequence) {
            return;
        }

        $state = $this->sequence->tick();

        if ($state->equals(State::FINISHED())) {
            $this->terminateSequence();
        }
    }

    private function terminateSequence()
    {
        $this->sequence = null;
    }
}
