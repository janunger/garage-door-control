<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\EndToEnd\EventLoop;

use JUIT\MailHog\MailHogClient;
use JUIT\MailHog\Message;

class MessagingTest extends EventLoopTestCase
{
    /** @var MailHogClient */
    private static $mailHog;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$mailHog = MailHogClient::create('http://mailhog:8025');
    }

    protected function setUp()
    {
        parent::setUp();

        static::$mailHog->deleteAll();
    }

    /** @test */
    public function it_sends_a_mail_with_door_state_on_startup()
    {
        $this->piFace->setPinOn($this->inputPinIdDoorClosed);
        $this->piFace->setPinOff($this->inputPinIdDoorOpened);

        static::$eventLoop->start();

        $messages = $this->fetchMailHogMessages();
        static::assertCount(1, $messages);
        static::assertRegExp('/^Watchdog restarted, door closed/', $messages[0]->getSubject());
    }

    /**
     * @test
     * @depends it_sends_a_mail_with_door_state_on_startup
     */
    public function it_sends_a_mail_on_door_opening()
    {
        $this->piFace->setPinOff($this->inputPinIdDoorClosed);

        $messages = $this->fetchMailHogMessages();
        static::assertCount(1, $messages);
        static::assertRegExp('/^DOOR OPENING/', $messages[0]->getSubject());
    }

    /**
     * @test
     * @depends it_sends_a_mail_on_door_opening
     */
    public function it_sends_a_mail_on_door_closed_again()
    {
        $this->piFace->setPinOn($this->inputPinIdDoorClosed);

        $messages = $this->fetchMailHogMessages();
        static::assertCount(1, $messages);
        static::assertRegExp('/^Door closed/', $messages[0]->getSubject());
    }

    /**
     * @test
     * @depends it_sends_a_mail_on_door_closed_again
     */
    public function it_sends_a_mail_on_hardware_error()
    {
        $this->piFace->setPinOn($this->inputPinIdDoorClosed);
        $this->piFace->setPinOn($this->inputPinIdDoorOpened);

        $messages = $this->fetchMailHogMessages();
        static::assertCount(1, $messages);
        static::assertRegExp('/^HARDWARE ERROR/', $messages[0]->getSubject());
    }

    /** @return Message[] */
    private function fetchMailHogMessages($numberToWaitFor = 1, $timeout = 5): array
    {
        $start = time();
        while (true) {
            $messages = static::$mailHog->fetchAll();
            if (count($messages) >= $numberToWaitFor) {
                return $messages;
            }
            if (time() > $start + $timeout) {
                $this->fail('MailHog did not receive the expected number of messages within timeout');
            }
            usleep(300000);
        }
    }

    public static function tearDownAfterClass()
    {
        static::$mailHog->deleteAll();

        parent::tearDownAfterClass();
    }
}
