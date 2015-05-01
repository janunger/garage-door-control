<?php

namespace GDC\Tests\WatchDog;

use Carbon\Carbon;
use GDC\Door\HardwareErrorException;
use GDC\Door\State;

class WatchDogTest extends WatchDogTestCase
{
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

        $SUT = $this->createSUTInstance();
        $SUT->execute();
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

        $SUT = $this->createSUTInstance();
        $SUT->execute();

        Carbon::setTestNow($date2);
        $SUT->execute();
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
        $SUT = $this->createSUTInstance();
        $SUT->execute();

        $this->messenger
            ->expects($this->once())->method('sendMessageAfterDoorClosed');

        Carbon::setTestNow($date2);
        $SUT->execute();
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

        $SUT = $this->createSUTInstance();
        $SUT->execute();
    }
}
