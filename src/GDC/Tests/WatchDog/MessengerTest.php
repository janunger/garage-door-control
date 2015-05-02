<?php

namespace GDC\Tests\WatchDog;

use GDC\Tests\AbstractTestCase;
use GDC\WatchDog\Messenger;

class MessengerTest extends AbstractTestCase
{
    /**
     * @var MailerMock
     */
    private $mailer;

    /**
     * @var Messenger
     */
    private $SUT;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $phpMock;

    protected function setUp()
    {
        $this->mailer = new MailerMock();
        $this->SUT    = new Messenger(
            $this->mailer,
            'sender@example.com',
            'Sender',
            'recipient@example.com',
            'Recipient'
        );
        $this->phpMock = \PHPUnit_Extension_FunctionMocker::start($this, 'GDC\Watchdog')
            ->mockFunction('date')
            ->getMock();
    }

    /**
     * @test
     */
    public function it_should_send_the_expected_mail_on_watchdog_restarted()
    {
        $this->phpMock->expects($this->atLeast(1))->method('date')->with('Y-m-d H:i:s')->willReturn('2015-05-02 14:30:00');

        $this->SUT->onWatchdogRestart();

        $message = $this->mailer->getMessage();
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals(['sender@example.com' => 'Sender'], $message->getFrom());
        $this->assertEquals(['recipient@example.com' => 'Recipient'], $message->getTo());
        $this->assertEquals('Watchdog restarted - 2015-05-02 14:30:00', $message->getSubject());
        $this->assertEquals('Watchdog restarted - 2015-05-02 14:30:00', $message->getBody());
    }

    /**
     * @test
     */
    public function it_should_send_the_expected_mail_on_door_opening()
    {
        $this->phpMock->expects($this->atLeast(1))->method('date')->with('Y-m-d H:i:s')->willReturn('2015-05-02 14:33:00');

        $this->SUT->onDoorOpening();

        $message = $this->mailer->getMessage();
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals(['sender@example.com' => 'Sender'], $message->getFrom());
        $this->assertEquals(['recipient@example.com' => 'Recipient'], $message->getTo());
        $this->assertEquals('Door opening - 2015-05-02 14:33:00', $message->getSubject());
        $this->assertEquals('Door opening - 2015-05-02 14:33:00', $message->getBody());
    }

    /**
     * @test
     */
    public function it_should_send_the_expected_mail_on_door_closed()
    {
        $this->phpMock->expects($this->atLeast(1))->method('date')->with('Y-m-d H:i:s')->willReturn('2015-05-02 14:34:00');

        $this->SUT->onDoorClosed();

        $message = $this->mailer->getMessage();
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals(['sender@example.com' => 'Sender'], $message->getFrom());
        $this->assertEquals(['recipient@example.com' => 'Recipient'], $message->getTo());
        $this->assertEquals('Door closed - 2015-05-02 14:34:00', $message->getSubject());
        $this->assertEquals('Door closed - 2015-05-02 14:34:00', $message->getBody());
    }

    /**
     * @test
     */
    public function it_should_send_the_expected_mail_on_hardware_error()
    {
        $this->phpMock->expects($this->atLeast(1))->method('date')->with('Y-m-d H:i:s')->willReturn('2015-05-02 14:35:00');

        $this->SUT->onHardwareError();

        $message = $this->mailer->getMessage();
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals(['sender@example.com' => 'Sender'], $message->getFrom());
        $this->assertEquals(['recipient@example.com' => 'Recipient'], $message->getTo());
        $this->assertEquals('Hardware error - 2015-05-02 14:35:00', $message->getSubject());
        $this->assertEquals('Hardware error - 2015-05-02 14:35:00', $message->getBody());
    }
}
