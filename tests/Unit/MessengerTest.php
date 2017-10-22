<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit;

use JUIT\GDC\Door\State;
use JUIT\GDC\Event\WatchDogRestartedEvent;
use JUIT\GDC\WatchDog\MessageFactory;
use JUIT\GDC\WatchDog\Messenger;
use PHPUnit\Extension\FunctionMocker;
use PHPUnit\Framework\TestCase;

class MessengerTest extends TestCase
{
    /** @var Messenger */
    private $SUT;

    /** @var \Swift_Mailer|\PHPUnit_Framework_MockObject_MockObject */
    private $mailer;

    /** @var MessageFactory|\PHPUnit_Framework_MockObject_MockObject */
    private $messageFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $phpMock;

    protected function setUp()
    {
        parent::setUp();
        $this->phpMock        = FunctionMocker::start($this, 'JUIT\\GDC\\WatchDog')
            ->mockFunction('date')
            ->getMock();
        $this->mailer         = $this->createMock(\Swift_Mailer::class);
        $this->messageFactory = $this->createMock(MessageFactory::class);
        $this->SUT            = new Messenger($this->mailer, $this->messageFactory);
    }

    /** @test */
    public function it_sends_the_expected_mail_on_watchdog_restarted_and_door_closed()
    {
        $this->phpMock
            ->expects(static::any())->method('date')->with('Y-m-d H:i:s')
            ->willReturn('2017-10-22 23:00:00');
        $this->messageFactory
            ->expects(static::once())->method('createMessage')
            ->with('Watchdog restarted, door closed - 2017-10-22 23:00:00')
            ->willReturn($this->createMock(\Swift_Message::class));

        $this->SUT->onWatchdogRestart(new WatchDogRestartedEvent(State::CLOSED()));
    }

    /** @test */
    public function it_sends_the_expected_mail_on_watchdog_restarted_and_door_opened()
    {
        $this->phpMock
            ->expects(static::any())->method('date')->with('Y-m-d H:i:s')
            ->willReturn('2017-10-22 23:01:00');
        $this->messageFactory
            ->expects(static::once())->method('createMessage')
            ->with('Watchdog restarted, DOOR OPENED - 2017-10-22 23:01:00')
            ->willReturn($this->createMock(\Swift_Message::class));

        $this->SUT->onWatchdogRestart(new WatchDogRestartedEvent(State::OPENED()));
    }

    /** @test */
    public function it_sends_the_expected_mail_on_watchdog_restarted_and_door_in_unknown_state()
    {
        $this->phpMock
            ->expects(static::any())->method('date')->with('Y-m-d H:i:s')
            ->willReturn('2017-10-22 23:05:00');

        $this->messageFactory
            ->expects(static::once())->method('createMessage')
            ->with('Watchdog restarted, DOOR UNKNOWN - 2017-10-22 23:05:00')
            ->willReturn($this->createMock(\Swift_Message::class));

        $this->SUT->onWatchdogRestart(new WatchDogRestartedEvent(State::UNKNOWN()));
    }

    /** @test */
    public function it_sends_the_expected_mail_on_door_opening()
    {
        $this->phpMock
            ->expects(static::any())->method('date')->with('Y-m-d H:i:s')
            ->willReturn('2017-10-22 23:00:00');
        $this->messageFactory
            ->expects(static::once())->method('createMessage')
            ->with('DOOR OPENING - 2017-10-22 23:00:00')
            ->willReturn($this->createMock(\Swift_Message::class));

        $this->SUT->onDoorOpening();
    }

    /** @test */
    public function it_sends_the_expected_mail_on_door_closed()
    {
        $this->phpMock
            ->expects(static::any())->method('date')->with('Y-m-d H:i:s')
            ->willReturn('2017-10-22 23:00:00');
        $this->messageFactory
            ->expects(static::once())->method('createMessage')
            ->with('Door closed - 2017-10-22 23:00:00')
            ->willReturn($this->createMock(\Swift_Message::class));

        $this->SUT->onDoorClosed();
    }

    /** @test */
    public function it_sends_the_expected_mail_on_hardware_error()
    {
        $this->phpMock
            ->expects(static::any())->method('date')->with('Y-m-d H:i:s')
            ->willReturn('2017-10-22 23:00:00');
        $this->messageFactory
            ->expects(static::once())->method('createMessage')
            ->with('HARDWARE ERROR - 2017-10-22 23:00:00')
            ->willReturn($this->createMock(\Swift_Message::class));

        $this->SUT->onHardwareError();
    }
}
