<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit;

use JUIT\GDC\Door\Door;
use JUIT\GDC\Door\HardwareError;
use JUIT\GDC\Door\State;
use JUIT\PiFace\InputPin;
use JUIT\PiFace\OutputPin;
use PHPUnit\Framework\TestCase;

class DoorTest extends TestCase
{
    /** @var Door */
    private $SUT;

    /** @var InputPin|\PHPUnit_Framework_MockObject_MockObject */
    private $sensorClosed;

    /** @var InputPin|\PHPUnit_Framework_MockObject_MockObject */
    private $sensorOpened;

    /** @var OutputPin|\PHPUnit_Framework_MockObject_MockObject */
    private $actorMotor;

    protected function setUp()
    {
        parent::setUp();
        $this->sensorClosed = $this->createMock(InputPin::class);
        $this->sensorOpened = $this->createMock(InputPin::class);
        $this->actorMotor   = $this->createMock(OutputPin::class);
        $this->SUT          = new Door($this->sensorClosed, $this->sensorOpened, $this->actorMotor);
    }

    /** @test */
    public function it_tells_if_the_door_is_closed()
    {
        $this->sensorClosed->expects(static::any())->method('isOn')->willReturn(true);
        $this->sensorOpened->expects(static::any())->method('isOn')->willReturn(false);

        static::assertEquals(State::CLOSED(), $this->SUT->getState());
    }

    /** @test */
    public function it_tells_if_the_door_state_is_unknown_when_moving()
    {
        $this->sensorClosed->expects(static::any())->method('isOn')->willReturn(false);
        $this->sensorOpened->expects(static::any())->method('isOn')->willReturn(false);

        static::assertEquals(State::UNKNOWN(), $this->SUT->getState());
    }

    /** @test */
    public function it_tells_if_the_door_is_opened()
    {
        $this->sensorClosed->expects(static::any())->method('isOn')->willReturn(false);
        $this->sensorOpened->expects(static::any())->method('isOn')->willReturn(true);

        static::assertEquals(State::OPENED(), $this->SUT->getState());
    }

    /** @test */
    public function it_throws_an_exception_if_sensor_state_is_implausible()
    {
        $this->sensorClosed->expects(static::any())->method('isOn')->willReturn(true);
        $this->sensorOpened->expects(static::any())->method('isOn')->willReturn(true);

        $this->expectException(HardwareError::class);
        $this->SUT->getState();
    }

    /** @test */
    public function it_triggers_the_motor()
    {
        $this->actorMotor->expects(static::once())->method('trigger')->with(500);

        $this->SUT->triggerControl();
    }
}
