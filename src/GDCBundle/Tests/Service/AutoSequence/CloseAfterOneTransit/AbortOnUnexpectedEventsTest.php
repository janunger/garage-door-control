<?php

namespace GDCBundle\Tests\Service\AutoSequence\CloseAfterOneTransit;

use GDC\Door\State as DoorState;
use GDC\Tests\AbstractTestCase;
use GDCBundle\Service\AutoSequence\CloseAfterOneTransit;
use GDCBundle\Service\AutoSequence\State as SequenceState;
use GDCBundle\Service\TimeProvider;
use GDCBundle\Tests\Service\AutoSequence\DoorMock;
use GDCBundle\Tests\Service\SensorLogger\InputPinMock;

class AbortOnUnexpectedEventsTest extends AbstractTestCase
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
    public function it_should_abort_if_door_is_closing_without_being_triggered()
    {
        TimeProvider::setTestMicrotime(1431120000.0000);
        $this->door->setState(DoorState::CLOSED());
        $this->sensorPhotoInterrupter->setIsOn(false);

        // It should not do anything right after instantiation.
        $SUT = new CloseAfterOneTransit($this->door, $this->sensorPhotoInterrupter);
        $this->assertEquals(0, $this->door->getTriggerControlCount());

        // It should trigger the door.
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was expected to be triggered once.');

        // It should do nothing when door starts moving.
        TimeProvider::setTestMicrotime(1431120001.0000);
        $this->door->setState(DoorState::UNKNOWN());
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');

        // It should abort if the door closes immediately
        TimeProvider::setTestMicrotime(1431120002.0000);
        $this->door->setState(DoorState::CLOSED());
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered any more.');

        // It should not trigger the door any more, even if ticked again.
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered any more.');
    }

    /**
     * @test
     */
    public function it_should_abort_if_door_is_closing_after_transit_without_being_triggered()
    {
        TimeProvider::setTestMicrotime(1431120000.0000);
        $this->door->setState(DoorState::CLOSED());
        $this->sensorPhotoInterrupter->setIsOn(false);

        // It should not do anything right after instantiation.
        $SUT = new CloseAfterOneTransit($this->door, $this->sensorPhotoInterrupter);
        $this->assertEquals(0, $this->door->getTriggerControlCount());

        // It should trigger the door.
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was expected to be triggered once.');

        // It should do nothing when door starts moving.
        TimeProvider::setTestMicrotime(1431120001.0000);
        $this->door->setState(DoorState::UNKNOWN());
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');

        // It should ignore the photo interrupter within the first seconds when the door itself goes through it.
        TimeProvider::setTestMicrotime(1431120002.5000);
        $this->sensorPhotoInterrupter->setIsOn(true);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');
        TimeProvider::setTestMicrotime(1431120005.5000);
        $this->sensorPhotoInterrupter->setIsOn(false);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');

        // It should not trigger the door when the door is still moving and the photo interrupter goes on and off.
        TimeProvider::setTestMicrotime(1431120007.0000);
        $this->sensorPhotoInterrupter->setIsOn(true);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');
        TimeProvider::setTestMicrotime(1431120008.0000);
        $this->sensorPhotoInterrupter->setIsOn(false);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');

        // It should abort if the door closes
        TimeProvider::setTestMicrotime(1431120009.0000);
        $this->door->setState(DoorState::CLOSED());
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered any more.');

        // It should not trigger the door any more, even if ticked again.
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered any more.');
    }

    /**
     * @test
     */
    public function it_should_abort_if_door_was_unknown_and_is_closing_without_being_triggered()
    {
        TimeProvider::setTestMicrotime(1431120000.0000);
        $this->door->setState(DoorState::UNKNOWN());
        $this->sensorPhotoInterrupter->setIsOn(false);

        // It should not do anything right after instantiation.
        $SUT = new CloseAfterOneTransit($this->door, $this->sensorPhotoInterrupter);
        $this->assertEquals(0, $this->door->getTriggerControlCount());

        // It should not trigger the door when the door is still moving and the photo interrupter goes on and off.
        TimeProvider::setTestMicrotime(1431120001.0000);
        $this->sensorPhotoInterrupter->setIsOn(true);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(0, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');
        TimeProvider::setTestMicrotime(1431120002.0000);
        $this->sensorPhotoInterrupter->setIsOn(false);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(0, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');

        // It should abort if the door closes
        TimeProvider::setTestMicrotime(1431120003.0000);
        $this->door->setState(DoorState::CLOSED());
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(0, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered any more.');

        // It should not trigger the door any more, even if ticked again.
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(0, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered any more.');
    }

    /**
     * @test
     */
    public function it_should_abort_if_door_has_opened_and_moves_without_being_triggered()
    {
        TimeProvider::setTestMicrotime(1431120000.0000);

        // It should not do anything right after instantiation.
        $SUT = new CloseAfterOneTransit($this->door, $this->sensorPhotoInterrupter);
        $this->assertEquals(0, $this->door->getTriggerControlCount());

        // It should trigger the door.
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was expected to be triggered once.');

        // It should do nothing when door starts moving.
        TimeProvider::setTestMicrotime(1431120001.0000);
        $this->door->setState(DoorState::UNKNOWN());
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');

        // It should ignore the photo interrupter within the first seconds when the door itself goes through it.
        TimeProvider::setTestMicrotime(1431120002.5000);
        $this->sensorPhotoInterrupter->setIsOn(true);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');
        TimeProvider::setTestMicrotime(1431120005.5000);
        $this->sensorPhotoInterrupter->setIsOn(false);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');

        // It should not trigger the door when the door is completely opened and the photo interrupter does nothing.
        TimeProvider::setTestMicrotime(1431120010.0000);
        $this->door->setState(DoorState::OPENED());
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');

        // It should abort if the door moves
        TimeProvider::setTestMicrotime(1431120011.0000);
        $this->door->setState(DoorState::UNKNOWN());
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered any more.');

        // It should not trigger the door any more, even if ticked again.
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered any more.');
    }
}