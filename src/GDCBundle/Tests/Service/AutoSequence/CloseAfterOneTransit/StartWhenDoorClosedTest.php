<?php

namespace GDCBundle\Tests\Service\AutoSequence\CloseAfterOneTransit;

use GDC\Door\State as DoorState;
use GDC\Tests\AbstractTestCase;
use GDCBundle\Service\AutoSequence\CloseAfterOneTransit;
use GDCBundle\Service\AutoSequence\State as SequenceState;
use GDCBundle\Service\TimeProvider;
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
        $this->sensorPhotoInterrupter = new InputPinMock();
    }

    /**
     * @test
     */
    public function it_should_close_the_door_if_door_initially_closed_and_transit_after_door_is_opened()
    {
        $this->door->setState(DoorState::CLOSED());
        $this->sensorPhotoInterrupter->setIsOn(false);

        // It should not do anything right after instantiation.
        $SUT = new CloseAfterOneTransit($this->door, $this->sensorPhotoInterrupter);
        $this->assertEquals(0, $this->door->getTriggerControlCount());

        // It should trigger the door.
        TimeProvider::setTestMicrotime('0.00000000 1431110000');
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was expected to be triggered once.');

        // It should do nothing when door starts moving.
        TimeProvider::setTestMicrotime('0.00000000 1431110001');
        $this->door->setState(DoorState::UNKNOWN());
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');

        // It should ignore the photo interrupter within the first seconds when the door itself goes through it.
        TimeProvider::setTestMicrotime('0.50000000 1431110002');
        $this->sensorPhotoInterrupter->setIsOn(true);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');
        TimeProvider::setTestMicrotime('0.50000000 1431110005');
        $this->sensorPhotoInterrupter->setIsOn(false);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');

        // It should not trigger the door when the door is completely opened and the photo interrupter does nothing.
        TimeProvider::setTestMicrotime('0.00000000 1431110010');
        $this->door->setState(DoorState::OPENED());
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');

        // It should trigger the door again when the door is opened and the photo interrupter goes on and off.
        TimeProvider::setTestMicrotime('0.00000000 1431110011');
        $this->sensorPhotoInterrupter->setIsOn(true);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');
        TimeProvider::setTestMicrotime('0.00000000 1431110012');
        $this->sensorPhotoInterrupter->setIsOn(false);
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(2, $this->door->getTriggerControlCount(), 'Door was expected to be triggered once again.');

        // It should not trigger the door any more, even if ticked again.
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(2, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered any more.');
    }
}
