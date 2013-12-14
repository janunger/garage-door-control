<?php

namespace GDC\WatchDog\Tests;

use Carbon\Carbon;
use GDC\Door;
use GDC\Door\HardwareErrorException;
use GDC\WatchDog\WatchDog;

class WatchDogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function sends_message_immediately_after_instantiation()
    {
        Carbon::setTestNow(Carbon::now());
        $door = $this->createDoorMock();
        $messenger = $this->createMessengerMock();

        $door
            ->expects($this->any())->method('getState')
            ->will($this->returnValue(Door::STATE_CLOSED));
        $messenger
            ->expects($this->once())->method('send')
            ->with(
                $this->equalTo(Door::STATE_CLOSED),
                Carbon::now()
            );

        $sut = new WatchDog($door, $messenger);
        $sut->execute();
    }

    /**
     * @test
     */
    public function sends_message_after_state_changes()
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
            ->expects($this->at(0))->method('send')
            ->with(
                $this->equalTo(Door::STATE_CLOSED),
                $date1
            );
        $messenger
            ->expects($this->at(1))->method('send')
            ->with(
                $this->equalTo(Door::STATE_UNKNOWN),
                $date2
            );

        $sut = new WatchDog($door, $messenger);
        $sut->execute();

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
            ->expects($this->once())->method('send')
            ->with(
                $this->equalTo('hardware_error'),
                Carbon::now()
            );

        $sut = new WatchDog($door, $messenger);
        $sut->execute();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
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
     * @return \PHPUnit_Framework_MockObject_MockObject
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
