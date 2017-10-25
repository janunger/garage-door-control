<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit;

use JUIT\GDC\Door\Door;
use JUIT\GDC\Door\State;
use JUIT\GDC\Model\InputPinId;
use JUIT\GDC\Model\InputPinIdDoorClosed;
use JUIT\GDC\Model\InputPinIdDoorOpened;
use JUIT\PiFace\InputPin;
use JUIT\PiFace\InputPinState;
use JUIT\PiFace\OutputPin;
use JUIT\PiFace\PiFace;
use PHPUnit\Framework\TestCase;

class DoorTest extends TestCase
{
    /** @var Door */
    private $SUT;

    /** @var PiFace|\PHPUnit_Framework_MockObject_MockObject */
    private $piFace;

    /** @var OutputPin|\PHPUnit_Framework_MockObject_MockObject */
    private $actorMotor;

    protected function setUp()
    {
        parent::setUp();
        $this->piFace = $this->createMock(PiFace::class);
        $this->actorMotor = $this->createMock(OutputPin::class);
        $this->SUT = new Door(
            $this->piFace,
            new InputPinIdDoorClosed(0),
            new InputPinIdDoorOpened(1),
            $this->actorMotor
        );
    }

    /** @test */
    public function it_tells_if_the_door_is_closed()
    {
        $state = new InputPinState('10000000');
        $this->piFace->expects(static::any())->method('readInputPins')->willReturn($state);

        static::assertEquals(State::CLOSED(), $this->SUT->getState());
    }

    /** @test */
    public function it_tells_if_the_door_state_is_unknown_when_moving()
    {
        $state = new InputPinState('00000000');
        $this->piFace->expects(static::any())->method('readInputPins')->willReturn($state);

        static::assertEquals(State::UNKNOWN(), $this->SUT->getState());
    }

    /** @test */
    public function it_tells_if_the_door_is_opened()
    {
        $state = new InputPinState('01000000');
        $this->piFace->expects(static::any())->method('readInputPins')->willReturn($state);

        static::assertEquals(State::OPENED(), $this->SUT->getState());
    }

    /** @test */
    public function it_tells_if_sensor_state_is_implausible()
    {
        $state = new InputPinState('11000000');
        $this->piFace->expects(static::any())->method('readInputPins')->willReturn($state);

        static::assertEquals(State::HARDWARE_ERROR(), $this->SUT->getState());
    }

    /** @test */
    public function it_triggers_the_motor()
    {
        $this->actorMotor->expects(static::once())->method('trigger')->with(500);

        $this->SUT->triggerControl();
    }
}
