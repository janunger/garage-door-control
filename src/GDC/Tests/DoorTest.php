<?php

namespace GDC\Tests;

use GDC\Door\Door;
use GDC\Door\State;
use GDCBundle\Tests\Service\SensorLogger\InputPinMock;
use Pkj\Raspberry\PiFace\OutputPin;

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
     * @var OutputPin|\PHPUnit_Framework_MockObject_MockObject
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
        $this->motorTrigger = $this->createMock('\Pkj\Raspberry\PiFace\OutputPin');
        $this->SUT = new Door($this->sensorClosed, $this->sensorOpened, $this->motorTrigger);
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
}