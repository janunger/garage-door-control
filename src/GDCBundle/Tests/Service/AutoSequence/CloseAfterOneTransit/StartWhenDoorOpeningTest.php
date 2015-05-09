<?php

namespace GDCBundle\Tests\Service\AutoSequence\CloseAfterOneTransit;

use GDC\Door\State as DoorState;
use GDC\Tests\AbstractTestCase;
use GDCBundle\Service\AutoSequence\CloseAfterOneTransit;
use GDCBundle\Service\AutoSequence\State as SequenceState;
use GDCBundle\Service\TimeProvider;
use GDCBundle\Tests\Service\AutoSequence\DoorMock;
use GDCBundle\Tests\Service\SensorLogger\InputPinMock;

class StartWhenDoorOpeningTest extends AbstractTestCase
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
        $this->door->setState(DoorState::UNKNOWN());

        $this->sensorPhotoInterrupter = new InputPinMock();
        $this->sensorPhotoInterrupter->setIsOn(false);
    }

    /**
     * @test
     */
    public function it_should_close_the_door_on_transit_after_door_is_opened()
    {
        TimeProvider::setTestMicrotime(1431120000.0000);

        // It should not do anything right after instantiation.
        $SUT = new CloseAfterOneTransit($this->door, $this->sensorPhotoInterrupter);
        $this->assertEquals(0, $this->door->getTriggerControlCount());

        // It should not trigger the door when the door is completely opened and the photo interrupter does nothing.
        TimeProvider::setTestMicrotime(1431120001.0000);
        $this->door->setState(DoorState::OPENED());
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(0, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');

        // It should trigger the door when the door is opened and the photo interrupter goes on and off.
        TimeProvider::setTestMicrotime(1431120002.0000);
        $this->sensorPhotoInterrupter->setIsOn(true);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(0, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');
        TimeProvider::setTestMicrotime(1431120003.0000);
        $this->sensorPhotoInterrupter->setIsOn(false);
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was expected to be triggered once again.');

        // It should not trigger the door any more, even if ticked again.
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered any more.');
    }
}