<?php

namespace GDCBundle\Tests\Service\AutoSequence;

use GDC\CommandQueue\Command;
use GDC\Tests\AbstractTestCase;
use GDCBundle\Service\AutoSequence\Factory;

class FactoryTest extends AbstractTestCase
{
    /**
     * @var Factory
     */
    private $SUT;

    protected function setUp()
    {
        $this->SUT = new Factory(
            $this->createMock('GDC\Door\DoorInterface'),
            $this->createMock('Pkj\Raspberry\PiFace\InputPin')
        );
    }

    /**
     * @test
     */
    public function it_should_create_a_trigger_door_sequence_for_a_trigger_door_command()
    {
        $this->assertInstanceOf(
            'GDCBundle\Service\AutoSequence\TriggerDoor',
            $this->SUT->createSequenceFor(Command::TRIGGER_DOOR())
        );
    }

    /**
     * @test
     */
    public function it_should_create_a_close_after_one_transit_sequence_for_a_close_after_one_command()
    {
        $this->assertInstanceOf(
            'GDCBundle\Service\AutoSequence\CloseAfterOneTransit',
            $this->SUT->createSequenceFor(Command::CLOSE_AFTER_ONE_TRANSIT())
        );
    }
}