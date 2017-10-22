<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit;

use JUIT\GDC\Door\Door;
use JUIT\GDC\Door\HardwareError;
use JUIT\GDC\Door\State;
use JUIT\GDC\Event\WatchDogEvents;
use JUIT\GDC\Event\WatchDogRestartedEvent;
use JUIT\GDC\WatchDog\Messenger;
use JUIT\GDC\WatchDog\WatchDog;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WatchDogTest extends TestCase
{
    /** @var Door|\PHPUnit_Framework_MockObject_MockObject */
    private $door;

    /** @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $eventDispatcher;

    /** @var Messenger */
    private $messenger;

    protected function setUp()
    {
        parent::setUp();
        $this->door            = $this->createMock(Door::class);
        $this->messenger       = $this->createMock(Messenger::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
    }

    /** @test */
    public function it_raises_a_watchdog_restarted_event_on_startup()
    {
        $this->door->expects(static::any())->method('getState')->willReturn(State::CLOSED());

        $this->eventDispatcher
            ->expects(static::once())->method('dispatch')
            ->with(WatchDogEvents::RESTARTED, new WatchDogRestartedEvent(State::CLOSED()));

        $this->createSUTInstance();
    }

    /** @test */
    public function it_does_not_raise_an_event_if_state_has_not_changed()
    {
        // $door->getState gets invoked on instantiation
        $this->door->expects(static::at(0))->method('getState')->willReturn(State::CLOSED());
        $this->door->expects(static::at(1))->method('getState')->willReturn(State::CLOSED());
        $this->door->expects(static::at(2))->method('getState')->willReturn(State::CLOSED());

        $this->eventDispatcher->expects(static::once())->method('dispatch')->with(WatchDogEvents::RESTARTED);

        $SUT = $this->createSUTInstance();
        $SUT->execute();
        $SUT->execute();
    }

    /** @test */
    public function it_raises_a_door_opening_event_when_door_leaves_closed_position()
    {
        // $door->getState gets invoked on instantiation
        $this->door->expects(static::at(0))->method('getState')->willReturn(State::CLOSED());
        $this->door->expects(static::at(1))->method('getState')->willReturn(State::CLOSED());
        $this->door->expects(static::at(2))->method('getState')->willReturn(State::UNKNOWN());

        $this->eventDispatcher->expects(static::at(0))->method('dispatch')->with(WatchDogEvents::RESTARTED);
        $this->eventDispatcher->expects(static::at(1))->method('dispatch')->with(WatchDogEvents::DOOR_OPENING);

        $SUT = $this->createSUTInstance();
        $SUT->execute();
        $SUT->execute();
    }

    /** @test */
    public function it_raises_a_door_opening_event_when_door_reaches_closed_position()
    {
        // $door->getState gets invoked on instantiation
        $this->door->expects(static::at(0))->method('getState')->willReturn(State::UNKNOWN());
        $this->door->expects(static::at(1))->method('getState')->willReturn(State::UNKNOWN());
        $this->door->expects(static::at(2))->method('getState')->willReturn(State::CLOSED());

        $this->eventDispatcher->expects(static::at(0))->method('dispatch')->with(WatchDogEvents::RESTARTED);
        $this->eventDispatcher->expects(static::at(1))->method('dispatch')->with(WatchDogEvents::DOOR_CLOSED);

        $SUT = $this->createSUTInstance();
        $SUT->execute();
        $SUT->execute();
    }

    /** @test */
    public function it_handles_a_hardware_error_on_instantiation()
    {
        $this->door
            ->expects($this->any())->method('getState')
            ->will($this->throwException(new HardwareError()));

        $this->eventDispatcher->expects($this->once())->method('dispatch')->with(WatchDogEvents::HARDWARE_ERROR);
        $this->createSUTInstance();
    }

    /** @test */
    public function it_handles_a_hardware_error_when_running()
    {
        $this->door->expects(static::at(0))->method('getState')->willReturn(State::CLOSED());
        $this->door
            ->expects(static::at(1))->method('getState')
            ->will(static::throwException(new HardwareError()));
        $SUT = $this->createSUTInstance();

        $this->eventDispatcher->expects($this->once())->method('dispatch')->with(WatchDogEvents::HARDWARE_ERROR);
        $SUT->execute();
    }

    private function createSUTInstance(): WatchDog
    {
        return new WatchDog($this->door, $this->messenger, $this->eventDispatcher);
    }
}
