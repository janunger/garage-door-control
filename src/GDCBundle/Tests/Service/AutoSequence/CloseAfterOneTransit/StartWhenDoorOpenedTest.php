<?php

namespace GDCBundle\Tests\Service\AutoSequence\CloseAfterOneTransit;

use GDC\Door\State;
use GDC\Tests\AbstractTestCase;
use GDCBundle\Model\Microtime;
use GDCBundle\Service\AutoSequence\CloseAfterOneTransit;
use GDCBundle\Service\AutoSequence\State as SequenceState;
use GDCBundle\Service\TimeProvider;
use GDCBundle\Tests\Service\AutoSequence\DoorMock;
use GDCBundle\Tests\Service\SensorLogger\InputPinMock;

class StartWhenDoorOpenedTest extends AbstractTestCase
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
        $this->door->setState(State::OPENED());

        $this->sensorPhotoInterrupter = new InputPinMock();
        $this->sensorPhotoInterrupter->setIsOn(false);
    }

    /**
     * @test
     */
    public function it_should_close_the_door_after_transit()
    {
        TimeProvider::setTestMicrotime(new Microtime(143112000));

        // It should not do anything right after instantiation.
        $SUT = new CloseAfterOneTransit($this->door, $this->sensorPhotoInterrupter);
        $this->assertEquals(0, $this->door->getTriggerControlCount());

        // It should trigger the door when the photo interrupter goes on and off.
        TimeProvider::setTestMicrotime(new Microtime(1431120001));
        $this->sensorPhotoInterrupter->setIsOn(true);
        $this->assertEquals(SequenceState::RUNNING(), $SUT->tick());
        $this->assertEquals(0, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered.');
        TimeProvider::setTestMicrotime(new Microtime(1431120002));
        $this->sensorPhotoInterrupter->setIsOn(false);
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was expected to be triggered once again.');

        // It should not trigger the door any more, even if ticked again.
        $this->assertEquals(SequenceState::FINISHED(), $SUT->tick());
        $this->assertEquals(1, $this->door->getTriggerControlCount(), 'Door was not expected to be triggered any more.');
    }
}