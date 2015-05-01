<?php

namespace GDC\Tests\WatchDog;

use Carbon\Carbon;
use GDC\Door\DoorInterface;
use GDC\Door\HardwareErrorException;
use GDC\Door\State;
use GDC\Tests\AbstractTestCase;
use GDC\WatchDog\Messenger;
use GDC\WatchDog\WatchDog;
use GDCBundle\Entity\DoorStateRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WatchDogTest extends AbstractTestCase
{
    /**
     * @var DoorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $door;

    /**
     * @var Messenger|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messenger;

    /**
     * @var DoorStateRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $doorStateRepository;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventDispatcher;

    /**
     * @var WatchDog
     */
    private $SUT;

    protected function setUp()
    {
        $this->door                = $this->createMock('GDC\Door\DoorInterface');
        $this->messenger           = $this->createMock('GDC\WatchDog\Messenger');
        $this->doorStateRepository = $this->createMock('GDCBundle\Entity\DoorStateRepository');
        $this->eventDispatcher     = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->SUT = new WatchDog($this->door, $this->messenger, $this->doorStateRepository, $this->eventDispatcher);
    }

    /**
     * @test
     */
    public function it_should_send_a_message_immediately_after_instantiation()
    {
        $this->door
            ->expects($this->any())->method('getState')
            ->will($this->returnValue(State::CLOSED()));
        $this->messenger
            ->expects($this->once())->method('sendMessageOnWatchdogRestart');

        $this->SUT->execute();
    }

    /**
     * @test
     */
    public function it_should_send_a_message_when_the_door_begins_to_open()
    {
        $date1 = Carbon::create(2013, 12, 14, 22, 0, 0);
        $date2 = Carbon::create(2013, 12, 14, 22, 0, 1);
        Carbon::setTestNow($date1);

        $this->door
            ->expects($this->any())->method('getState')
            ->will($this->returnCallback(function () use ($date1, $date2) {
                if (Carbon::now()->eq($date1)) {
                    return State::CLOSED();
                }

                return State::UNKNOWN();
            }));
        $this->messenger
            ->expects($this->once())->method('sendMessageOnDoorOpening');

        $this->SUT->execute();

        Carbon::setTestNow($date2);
        $this->SUT->execute();
    }

    /**
     * @test
     */
    public function it_should_send_a_message_when_the_door_has_finished_closing()
    {
        $date1 = Carbon::create(2013, 12, 14, 22, 0, 0);
        $date2 = Carbon::create(2013, 12, 14, 22, 0, 1);
        Carbon::setTestNow($date1);

        $this->door
            ->expects($this->any())->method('getState')
            ->will($this->returnCallback(function () use ($date1, $date2) {
                if (Carbon::now()->eq($date1)) {
                    return State::UNKNOWN();
                }

                return State::CLOSED();
            }));
        $this->SUT->execute();

        $this->messenger
            ->expects($this->once())->method('sendMessageAfterDoorClosed');

        Carbon::setTestNow($date2);
        $this->SUT->execute();
    }

    /**
     * @test
     */
    public function can_handle_hardware_error()
    {
        $this->door
            ->expects($this->any())->method('getState')
            ->will($this->throwException(new HardwareErrorException('Both sensors are on')));
        $this->messenger
            ->expects($this->once())->method('sendHardwareError');

        $this->SUT->execute();
    }
}
