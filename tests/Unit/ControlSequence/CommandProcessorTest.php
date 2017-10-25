<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit\ControlSequence;

use JUIT\GDC\ControlSequence\Command;
use JUIT\GDC\ControlSequence\CommandProcessor;
use JUIT\GDC\ControlSequence\Repository;
use JUIT\GDC\Event\CommandIssuedEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CommandProcessorTest extends TestCase
{
    /** @var CommandProcessor */
    private $SUT;

    /** @var Repository|\PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    /** @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $eventDispatcher;

    protected function setUp()
    {
        parent::setUp();
        $this->repository      = $this->createMock(Repository::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->SUT             = new CommandProcessor($this->repository, $this->eventDispatcher);
    }

    /**
     * @test
     */
    public function it_should_do_nothing_if_no_commands_present()
    {
        $this->repository->expects($this->any())->method('getCommands')->willReturn([]);
        $this->repository->expects($this->never())->method('delete');

        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->SUT->execute();
    }

    /**
     * @test
     */
    public function it_should_dispatch_one_event_for_one_command()
    {
        $command = Command::TRIGGER_DOOR();
        $this->repository->expects($this->any())->method('getCommands')->willReturn([$command]);
        $this->repository->expects($this->once())->method('delete')->with($command);
        $this->eventDispatcher
          ->expects($this->once())->method('dispatch')
          ->with(CommandIssuedEvent::NAME, new CommandIssuedEvent($command));

        $this->SUT->execute();
    }

    /**
     * @test
     */
    public function it_should_dispatch_an_event_for_each_command()
    {
        $command1 = Command::TRIGGER_DOOR();
        $command2 = Command::CLOSE_AFTER_ONE_TRANSIT();
        $this->repository->expects($this->at(0))->method('getCommands')->willReturn([$command1, $command2]);

        $this->eventDispatcher
          ->expects(static::at(0))->method('dispatch')
          ->with(CommandIssuedEvent::NAME, new CommandIssuedEvent($command1));
        $this->repository->expects(static::at(1))->method('delete')->with($command1);

        $this->eventDispatcher
          ->expects(static::at(1))->method('dispatch')
          ->with(CommandIssuedEvent::NAME, new CommandIssuedEvent($command2));
        $this->repository->expects(static::at(2))->method('delete')->with($command2);

        $this->SUT->execute();
    }
}
