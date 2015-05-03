<?php

namespace GDCBundle\Service;

use GDCBundle\Entity\CommandQueueEntryRepository;
use GDCBundle\Event\CommandIssuedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CommandProcessor
{
    /**
     * @var CommandQueueEntryRepository
     */
    private $commandQueueEntryRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(CommandQueueEntryRepository $commandQueueEntryRepository, EventDispatcherInterface $dispatcher)
    {
        $this->commandQueueEntryRepository = $commandQueueEntryRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute()
    {
        foreach ($this->commandQueueEntryRepository->getList() as $queueEntry) {
            $event = new CommandIssuedEvent($queueEntry->getCommand());
            $this->dispatcher->dispatch('gdc.command_issued', $event);
            $this->commandQueueEntryRepository->delete($queueEntry);
        }
    }
}
