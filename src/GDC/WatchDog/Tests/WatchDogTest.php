<?php

namespace GDC\WatchDog\Tests;

use Carbon\Carbon;
use GDC\Door;
use GDC\Door\HardwareErrorException;
use GDC\WatchDog\Messenger;
use GDC\WatchDog\WatchDog;

class WatchDogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_send_a_message_immediately_after_instantiation()
    {
        Carbon::setTestNow(Carbon::now());
        $door = $this->createDoorMock();
        $messenger = $this->createMessengerMock();

        $door
            ->expects($this->any())->method('getState')
            ->will($this->returnValue(Door::STATE_CLOSED));
        $messenger
            ->expects($this->once())->method('sendMessageOnWatchdogRestart');

        $sut = new WatchDog($door, $messenger);
        $sut->execute();
    }

    /**
     * @test
     */
    public function it_should_send_a_message_when_the_door_begins_to_open()
    {
        $door = $this->createDoorMock();
        $messenger = $this->createMessengerMock();

        $date1 = Carbon::create(2013, 12, 14, 22, 0, 0);
        $date2 = Carbon::create(2013, 12, 14, 22, 0, 1);
        Carbon::setTestNow($date1);

        $door
            ->expects($this->any())->method('getState')
            ->will($this->returnCallback(function () use ($date1, $date2) {
                if (Carbon::now()->eq($date1)) {
                    return Door::STATE_CLOSED;
                }
                return Door::STATE_UNKNOWN;
            }));
        $messenger
            ->expects($this->once())->method('sendMessageOnDoorOpening');

        $sut = new WatchDog($door, $messenger);
        $sut->execute();

        Carbon::setTestNow($date2);
        $sut->execute();
    }

    /**
     * @test
     */
    public function it_should_send_a_message_when_the_door_has_finished_closing()
    {
        $door = $this->createDoorMock();
        $messenger = $this->createMessengerMock();

        $date1 = Carbon::create(2013, 12, 14, 22, 0, 0);
        $date2 = Carbon::create(2013, 12, 14, 22, 0, 1);
        Carbon::setTestNow($date1);

        $door
            ->expects($this->any())->method('getState')
            ->will($this->returnCallback(function () use ($date1, $date2) {
                if (Carbon::now()->eq($date1)) {
                    return Door::STATE_UNKNOWN;
                }
                return Door::STATE_CLOSED;
            }));
        $sut = new WatchDog($door, $messenger);
        $sut->execute();

        $messenger
            ->expects($this->once())->method('sendMessageAfterDoorClosed');

        Carbon::setTestNow($date2);
        $sut->execute();
    }

    /**
     * @test
     */
    public function can_handle_hardware_error()
    {
        Carbon::setTestNow(Carbon::now());
        $door = $this->createDoorMock();
        $messenger = $this->createMessengerMock();

        $door
            ->expects($this->any())->method('getState')
            ->will($this->throwException(new HardwareErrorException('Both sensors are on')));
        $messenger
            ->expects($this->once())->method('sendHardwareError');

        $sut = new WatchDog($door, $messenger);
        $sut->execute();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Door
     */
    private function createDoorMock()
    {
        $door = $this
            ->getMockBuilder('\GDC\Door')
            ->disableOriginalConstructor()
            ->getMock();

        return $door;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Messenger
     */
    private function createMessengerMock()
    {
        $messenger = $this
            ->getMockBuilder('GDC\WatchDog\Messenger')
            ->disableOriginalConstructor()
            ->getMock();

        return $messenger;
    }
}
