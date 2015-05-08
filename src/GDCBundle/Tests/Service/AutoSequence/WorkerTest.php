<?php

namespace GDCBundle\Tests\Service\AutoSequence;

use GDC\CommandQueue\Command;
use GDC\Tests\AbstractTestCase;
use GDCBundle\Event\CommandIssuedEvent;
use GDCBundle\Service\AutoSequence\State;
use GDCBundle\Service\AutoSequence\Worker;

class WorkerTest extends AbstractTestCase
{
    /**
     * @var FactoryMock
     */
    private $factory;

    /**
     * @var Worker;
     */
    private $SUT;

    protected function setUp()
    {
        $this->factory = new FactoryMock(
            $this->createMock('GDC\Door\DoorInterface'),
            $this->createMock('Pkj\Raspberry\PiFace\InputPin')
        );
        $this->SUT     = new Worker($this->factory);
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
     * @ test
     */
    public function it_should_cancel_a_running_sequence_on_new_command_issued_and_start_another()
    {

    }
}