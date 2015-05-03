<?php

namespace GDC\Tests\WatchDog;

use GDC\Door\DoorInterface;
use GDC\Door\HardwareErrorException;
use GDC\Door\State;
use GDC\Tests\AbstractTestCase;
use GDC\WatchDog\Messenger;
use GDC\WatchDog\WatchDog;
use GDCBundle\Event\WatchDogEvents;
use GDCBundle\Event\WatchDogRestartedEvent;
use GDCBundle\Service\DoorStateWriter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WatchDogTest extends AbstractTestCase
{
    /**
     * @var DoorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $door;

    /**
     * @var Messenger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messenger;

    /**
     * @var DoorStateWriter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $doorStateWriter;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcher;

    protected function setUp()
    {
        $this->door            = $this->createMock('GDC\Door\DoorInterface');
        $this->messenger       = $this->createMock('GDC\WatchDog\Messenger');
        $this->doorStateWriter = $this->createMock('GDCBundle\Service\DoorStateWriter');
        $this->eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }

    /**
     * @test
     */
    public function it_should_raise_a_watchdog_restarted_event_on_startup()
    {
        $this->door->expects($this->atLeast(1))->method('getState')->willReturn(State::CLOSED());
        $this->eventDispatcher
            ->expects($this->once())->method('dispatch')
            ->with(WatchDogEvents::RESTARTED, new WatchDogRestartedEvent(State::CLOSED()));

        $this->createSUTInstance();
    }

    /**
     * @test
     */
    public function it_should_raise_a_door_opening_event_when_door_leaves_closed_position()
    {
        // $door->getState gets invoked on instantiation
        $this->door->expects($this->at(0))->method('getState')->willReturn(State::CLOSED());
        $this->door->expects($this->at(1))->method('getState')->willReturn(State::CLOSED());
        $this->door->expects($this->at(2))->method('getState')->willReturn(State::UNKNOWN());

        $this->eventDispatcher->expects($this->at(0))->method('dispatch')->with(WatchDogEvents::RESTARTED);
        $this->eventDispatcher->expects($this->at(1))->method('dispatch')->with(WatchDogEvents::DOOR_OPENING);

        $SUT = $this->createSUTInstance();
        $SUT->execute();
        $SUT->execute();
    }

    /**
     * @test
     */
    public function it_should_raise_a_door_closed_event_when_door_reaches_closed_position()
    {
        // $door->getState gets invoked on instantiation
        $this->door->expects($this->at(0))->method('getState')->willReturn(State::UNKNOWN());
        $this->door->expects($this->at(1))->method('getState')->willReturn(State::UNKNOWN());
        $this->door->expects($this->at(2))->method('getState')->willReturn(State::CLOSED());

        $this->eventDispatcher->expects($this->at(0))->method('dispatch')->with(WatchDogEvents::RESTARTED);
        $this->eventDispatcher->expects($this->at(1))->method('dispatch')->with(WatchDogEvents::DOOR_CLOSED);

        $SUT = $this->createSUTInstance();
        $SUT->execute();
        $SUT->execute();
    }

    /**
     * @test
     */
    public function can_handle_hardware_error_on_instantiation()
    {
        $this->door
            ->expects($this->any())->method('getState')
            ->will($this->throwException(new HardwareErrorException('Both sensors are on')));

        $this->eventDispatcher->expects($this->once())->method('dispatch')->with(WatchDogEvents::HARDWARE_ERROR);
        $this->createSUTInstance();
    }

    /**
     * @test
     */
    public function can_handle_hardware_error_when_running()
    {
        $this->door->expects($this->at(0))->method('getState')->willReturn(State::CLOSED());
        $this->door
            ->expects($this->at(1))->method('getState')
            ->will($this->throwException(new HardwareErrorException('Both sensors are on')));
        $SUT = $this->createSUTInstance();

        $this->eventDispatcher->expects($this->once())->method('dispatch')->with(WatchDogEvents::HARDWARE_ERROR);
        $SUT->execute();
    }

    /**
     * @test
     */
    public function it_should_update_the_door_state_on_every_cycle()
    {
        // $door->getState gets invoked on instantiation
        $this->door->expects($this->at(0))->method('getState')->will($this->returnValue(State::CLOSED()));
        $this->door->expects($this->at(1))->method('getState')->will($this->returnValue(State::CLOSED()));
        $this->door->expects($this->at(2))->method('getState')->will($this->returnValue(State::UNKNOWN()));

        $this->doorStateWriter->expects($this->at(0))->method('write')->with(State::CLOSED());
        $this->doorStateWriter->expects($this->at(1))->method('write')->with(State::UNKNOWN());

        $SUT = $this->createSUTInstance();

        $SUT->execute();
        $SUT->execute();
    }

    /**
     * @return WatchDog
     */
    private function createSUTInstance()
    {
        return new WatchDog($this->door, $this->messenger, $this->doorStateWriter, $this->eventDispatcher);
    }
}
