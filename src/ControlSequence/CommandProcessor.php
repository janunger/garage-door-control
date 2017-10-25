<?php

declare(strict_types=1);

namespace JUIT\GDC\ControlSequence;

use JUIT\GDC\Event\CommandIssuedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CommandProcessor
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(Repository $repository, EventDispatcherInterface $eventDispatcher)
    {
        $this->repository      = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute()
    {
        foreach ($this->repository->getCommands() as $command) {
            $this->eventDispatcher->dispatch(CommandIssuedEvent::NAME, new CommandIssuedEvent($command));
            $this->repository->delete($command);
        }
    }
}
