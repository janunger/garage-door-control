<?php

namespace GDC\Tests;

use GDC\Door\Door;
use GDC\Door\State;
use GDC\Tests\Door\DefectiveMotorTriggerMock;
use GDC\Tests\Door\MotorTriggerMock;
use GDCBundle\Tests\Service\SensorLogger\InputPinMock;

class DoorTest extends AbstractTestCase
{
    /**
     * @var InputPinMock
     */
    private $sensorClosed;

    /**
     * @var InputPinMock
     */
    private $sensorOpened;

    /**
     * @var MotorTriggerMock
     */
    private $motorTrigger;

    /**
     * @var Door
     */
    private $SUT;

    protected function setUp()
    {
        $this->sensorClosed = new InputPinMock();
        $this->sensorOpened = new InputPinMock();
        $this->motorTrigger = new MotorTriggerMock();
        $this->SUT          = new Door($this->sensorClosed, $this->sensorOpened, $this->motorTrigger);
    }

    /**
     * @test
     */
    public function it_should_tell_if_the_door_is_closed()
    {
        $this->sensorClosed->setIsOn(true);
        $this->sensorOpened->setIsOn(false);

        $this->assertEquals(State::CLOSED(), $this->SUT->getState());
    }

    /**
     * @test
     */
    public function it_should_tell_if_the_door_is_opened()
    {
        $this->sensorClosed->setIsOn(false);
        $this->sensorOpened->setIsOn(true);

        $this->assertEquals(State::OPENED(), $this->SUT->getState());
    }

    /**
     * @test
     */
    public function it_should_tell_if_the_door_state_is_unknown_when_moving()
    {
        $this->sensorClosed->setIsOn(false);
        $this->sensorOpened->setIsOn(false);

        $this->assertEquals(State::UNKNOWN(), $this->SUT->getState());
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception_if_sensor_state_is_implausible()
    {
        $this->sensorClosed->setIsOn(true);
        $this->sensorOpened->setIsOn(true);

        $this->setExpectedException('\GDC\Door\HardwareErrorException', 'Both sensors are on');
        $this->SUT->getState();
    }

    /**
     * @test
     */
    public function it_should_turn_the_motor_trigger_on_for_a_given_time_and_off_again()
    {
        $this->assertFalse($this->motorTrigger->isOn());

        $this->SUT->triggerControl();
        $events = $this->motorTrigger->getEvents();

        $this->assertCount(2, $events);

        $this->assertEquals('turnOn', $events[0]['action']);
        $this->assertEquals('turnOff', $events[1]['action']);

        $duration = bcsub($events[1]['microtime'], $events[0]['microtime'], 3);
        $this->assertGreaterThan(0.450, $duration);
        $this->assertLessThan(0.550, $duration);
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception_if_trigger_does_not_turn_on()
    {
        $SUT = new Door($this->sensorClosed, $this->sensorOpened, new DefectiveMotorTriggerMock(), 0);
        $this->setExpectedException('\GDC\Door\HardwareErrorException',
            "Motor trigger didn't turn on within expected timeout");
        $SUT->triggerControl();
    }
}