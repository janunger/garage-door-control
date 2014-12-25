<?php

namespace GDCBundle\Tests\Service;

use GDC\CommandQueue\Command;
use GDCBundle\Entity\CommandQueueEntry;
use GDCBundle\Event\CommandIssuedEvent;
use GDCBundle\Service\CommandProcessor;
use GDCBundle\Tests\AbstractTestCase;

class CommandProcessorTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_should_do_nothing_if_no_commands_present()
    {
        $repository = $this->createMock('GDCBundle\Entity\CommandQueueEntryRepository');
        $repository->expects($this->any())->method('getList')->will($this->returnValue([]));

        $dispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->never())->method('dispatch');

        $SUT = new CommandProcessor($repository, $dispatcher);

        $SUT->execute();
    }

    /**
     * @test
     */
    public function it_should_dispatch_one_event_for_one_command()
    {
        $repository = $this->createMock('GDCBundle\Entity\CommandQueueEntryRepository');
        $repository->expects($this->any())->method('getList')->will($this->returnValue([
            new CommandQueueEntry(Command::TRIGGER_DOOR())
        ]));

        $dispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher
          ->expects($this->once())->method('dispatch')
          ->with('gdc.command_issued', new CommandIssuedEvent(Command::TRIGGER_DOOR()));

        $SUT = new CommandProcessor($repository, $dispatcher);

        $SUT->execute();
    }

    /**
     * @test
     */
    public function it_should_dispatch_an_event_for_each_command()
    {
        $repository = $this->createMock('GDCBundle\Entity\CommandQueueEntryRepository');
        $repository->expects($this->any())->method('getList')->will($this->returnValue([
            new CommandQueueEntry(Command::TRIGGER_DOOR()),
            new CommandQueueEntry(Command::TRIGGER_DOOR())
        ]));

        $dispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher
          ->expects($this->exactly(2))->method('dispatch')
          ->with('gdc.command_issued', new CommandIssuedEvent(Command::TRIGGER_DOOR()));

        $SUT = new CommandProcessor($repository, $dispatcher);

        $SUT->execute();
    }
}
