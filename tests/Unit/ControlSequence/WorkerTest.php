<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit\ControlSequence;

use JUIT\GDC\ControlSequence\Command;
use JUIT\GDC\ControlSequence\Sequence\State;
use JUIT\GDC\ControlSequence\Worker;
use JUIT\GDC\Event\CommandIssuedEvent;
use JUIT\GDC\Tests\Unit\ControlSequence\Sequence\FactoryMock;
use JUIT\GDC\Tests\Unit\ControlSequence\Sequence\SequenceMock;
use PHPUnit\Framework\TestCase;

class WorkerTest extends TestCase
{
    /** @var FactoryMock */
    private $factory;

    /** @var Worker */
    private $SUT;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = new FactoryMock();
        $this->SUT     = new Worker($this->factory);
    }

    /** @test */
    public function it_does_nothing_if_no_command_issued()
    {
        $this->assertCount(0, $this->factory->getReceivedCommands());
        $this->SUT->tick();
        $this->assertCount(0, $this->factory->getReceivedCommands());
    }

    /** @test */
    public function it_starts_a_sequence_on_command_issued()
    {
        $sequence = new SequenceMock();
        $this->factory->setSequencesToReturn([$sequence]);
        static::assertCount(0, $this->factory->getReceivedCommands());

        $this->SUT->onCommandIssued(new CommandIssuedEvent(Command::TRIGGER_DOOR()));

        static::assertCount(1, $this->factory->getReceivedCommands());
        static::assertSame(0, $sequence->getReceivedTicks());

        $this->SUT->tick();

        static::assertSame(1, $sequence->getReceivedTicks());
    }

    /** @test */
    public function it_does_not_tick_a_finished_sequence_any_more()
    {
        $sequence = new SequenceMock();
        $this->factory->setSequencesToReturn([$sequence]);
        static::assertCount(0, $this->factory->getReceivedCommands());

        $this->SUT->onCommandIssued(new CommandIssuedEvent(Command::TRIGGER_DOOR()));
        static::assertCount(1, $this->factory->getReceivedCommands());
        static::assertSame(0, $sequence->getReceivedTicks());
        $this->SUT->tick();
        static::assertSame(1, $sequence->getReceivedTicks());

        $sequence->setState(State::FINISHED());
        $this->SUT->tick();
        static::assertSame(2, $sequence->getReceivedTicks());
        $this->SUT->tick();
        static::assertSame(2, $sequence->getReceivedTicks());
    }
}
