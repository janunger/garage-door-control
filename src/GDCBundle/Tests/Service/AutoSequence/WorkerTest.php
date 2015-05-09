<?php

namespace GDCBundle\Tests\Service\AutoSequence;

use GDC\CommandQueue\Command;
use GDC\Tests\AbstractTestCase;
use GDCBundle\Event\AutoSequenceStartedEvent;
use GDCBundle\Event\CommandIssuedEvent;
use GDCBundle\Model\AutoSequenceName;
use GDCBundle\Service\AutoSequence\State;
use GDCBundle\Service\AutoSequence\TriggerDoor;
use GDCBundle\Service\AutoSequence\Worker;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WorkerTest extends AbstractTestCase
{
    /**
     * @var FactoryMock
     */
    private $factory;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventDispatcher;

    /**
     * @var Worker;
     */
    private $SUT;

    protected function setUp()
    {
        $this->factory         = new FactoryMock(
            $this->createMock('GDC\Door\DoorInterface'),
            $this->createMock('Pkj\Raspberry\PiFace\InputPin')
        );
        $this->eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->SUT             = new Worker($this->factory, $this->eventDispatcher);
    }

    /**
     * @test
     */
    public function it_should_not_cause_any_error_if_no_command_issued()
    {
        $this->assertCount(0, $this->factory->getReceivedCommands());
        $this->SUT->tick();
        $this->assertCount(0, $this->factory->getReceivedCommands());
    }

    /**
     * @test
     */
    public function it_should_start_a_sequence_on_command_issued()
    {
        $sequence = new AutoSequenceMock();
        $this->factory->setSequencesToReturn([$sequence]);
        $this->assertCount(0, $this->factory->getReceivedCommands());

        $this->SUT->onCommandIssued(new CommandIssuedEvent(Command::TRIGGER_DOOR()));
        $this->assertCount(1, $this->factory->getReceivedCommands());
        $this->assertEquals(0, $sequence->getReceivedTicks());
        $this->SUT->tick();
        $this->assertEquals(1, $sequence->getReceivedTicks());
    }

    /**
     * @test
     */
    public function it_should_not_tick_a_finished_sequence_any_more()
    {
        $sequence = new AutoSequenceMock();
        $this->factory->setSequencesToReturn([$sequence]);
        $this->assertCount(0, $this->factory->getReceivedCommands());

        $this->SUT->onCommandIssued(new CommandIssuedEvent(Command::TRIGGER_DOOR()));
        $this->assertCount(1, $this->factory->getReceivedCommands());
        $this->assertEquals(0, $sequence->getReceivedTicks());
        $this->SUT->tick();
        $this->assertEquals(1, $sequence->getReceivedTicks());

        $sequence->setState(State::FINISHED());
        $this->SUT->tick();
        $this->assertEquals(2, $sequence->getReceivedTicks());
        $this->SUT->tick();
        $this->assertEquals(2, $sequence->getReceivedTicks());
    }

    /**
     * @test
     */
    public function it_should_cancel_a_running_sequence_on_new_command_issued_and_start_the_new_one()
    {
        $sequence1 = new AutoSequenceMock();
        $sequence2 = new AutoSequenceMock();
        $this->factory->setSequencesToReturn([$sequence1, $sequence2]);
        $this->assertCount(0, $this->factory->getReceivedCommands());

        $this->SUT->onCommandIssued(new CommandIssuedEvent(Command::CLOSE_AFTER_ONE_TRANSIT()));
        $this->assertCount(1, $this->factory->getReceivedCommands());
        $this->assertEquals(0, $sequence1->getReceivedTicks());
        $this->assertEquals(0, $sequence2->getReceivedTicks());
        $this->SUT->tick();
        $this->assertEquals(1, $sequence1->getReceivedTicks());
        $this->assertEquals(0, $sequence2->getReceivedTicks());

        $this->SUT->onCommandIssued(new CommandIssuedEvent(Command::TRIGGER_DOOR()));
        $this->assertCount(2, $this->factory->getReceivedCommands());
        $this->assertEquals(1, $sequence1->getReceivedTicks());
        $this->assertEquals(0, $sequence2->getReceivedTicks());
        $this->SUT->tick();
        $this->assertEquals(1, $sequence1->getReceivedTicks());
        $this->assertEquals(1, $sequence2->getReceivedTicks());
    }

    /**
     * @test
     */
    public function it_should_raise_an_event_on_new_sequence_started()
    {
        $sequence = new AutoSequenceMock(new AutoSequenceName(TriggerDoor::NAME));
        $this->factory->setSequencesToReturn([$sequence]);
        $this->assertCount(0, $this->factory->getReceivedCommands());

        $this->eventDispatcher
            ->expects($this->once())->method('dispatch')
            ->with('gdc.auto_sequence_started', new AutoSequenceStartedEvent(new AutoSequenceName(TriggerDoor::NAME)));

        $this->SUT->onCommandIssued(new CommandIssuedEvent(Command::TRIGGER_DOOR()));
    }

    /**
     * @test
     */
    public function it_should_raise_an_event_on_current_sequence_cancelled()
    {
        $sequence = new AutoSequenceMock(new AutoSequenceName(TriggerDoor::NAME));
        $this->factory->setSequencesToReturn([$sequence, null]);
        $this->assertCount(0, $this->factory->getReceivedCommands());

        $this->eventDispatcher
            ->expects($this->at(0))->method('dispatch')
            ->with('gdc.auto_sequence_started', new AutoSequenceStartedEvent(new AutoSequenceName(TriggerDoor::NAME)));
        $this->eventDispatcher
            ->expects($this->at(1))->method('dispatch')
            ->with('gdc.auto_sequence_terminated');

        $this->SUT->onCommandIssued(new CommandIssuedEvent(Command::CLOSE_AFTER_ONE_TRANSIT()));
        $this->SUT->onCommandIssued(new CommandIssuedEvent(Command::CANCEL()));
    }
}