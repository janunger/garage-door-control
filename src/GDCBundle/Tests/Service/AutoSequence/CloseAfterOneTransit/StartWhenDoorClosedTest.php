<?php

namespace GDCBundle\Tests\Service\AutoSequence\CloseAfterOneTransit;

use GDC\Door\State as DoorState;
use GDC\Tests\AbstractTestCase;
use GDCBundle\Service\AutoSequence\CloseAfterOneTransit;
use GDCBundle\Service\AutoSequence\State as SequenceState;
use GDCBundle\Tests\Service\AutoSequence\DoorMock;
use GDCBundle\Tests\Service\SensorLogger\InputPinMock;

class StartWhenDoorClosedTest extends AbstractTestCase
{
    /**
     * @var DoorMock
     */
    private $door;

    /**
     * @var InputPinMock
     */
    private $sensorPhotoInterrupter;

    protected function setUp()
    {
        $this->door = new DoorMock();
        $this->door->setState(DoorState::CLOSED());

        $this->sensorPhotoInterrupter = new InputPinMock();
        $this->sensorPhotoInterrupter->setIsOn(false);
    }

    /**
     * @test
     */
    public function it_should_execute_as_expected()
    {
        $SUT = new CloseAfterOneTransit($this->door, $this->sensorPhotoInterrupter);

        $this->assertEquals(0, $this->door->getTriggerControlCount());
        $this->assertEquals(DoorState::CLOSED(), $this->door->getState());

        // It should trigger the door.
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was expected to be triggered once.');

        // It should do nothing when door starts moving.
        $this->door->setState(DoorState::UNKNOWN());
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');
    }
}
