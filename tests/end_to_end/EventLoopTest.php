<?php

use Alex\MailCatcher\Client;

require_once __DIR__ . '/EventLoopTestCase.php';

class EventLoopTest extends EventLoopTestCase
{
    /** @var Client */
    private static $mailCatcher;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$mailCatcher = new Client('http://localhost:1080');
    }

    protected function setUp()
    {
        parent::setUp();

        self::$mailCatcher->purge();
    }

    /**
     * @test
     */
    public function it_should_send_a_mail_on_start_with_door_state()
    {
        self::setEmulatorInputPin(EmulatorPinId::DOOR_OPENED(), EmulatorPinValue::OFF());
        self::setEmulatorInputPin(EmulatorPinId::DOOR_CLOSED(), EmulatorPinValue::ON());
        self::$eventLoop->start();

        $this->waitUntilMailCatcherHasMessages();
        $this->assertEquals(1, self::$mailCatcher->getMessageCount());

        $message = self::$mailCatcher->searchOne();
        $this->assertRegExp('/^Watchdog restarted, door closed/', $message->getSubject());
    }

    /**
     * @test
     * @depends it_should_send_a_mail_on_start_with_door_state
     */
    public function it_should_send_a_mail_on_door_opening()
    {
        self::setEmulatorInputPin(EmulatorPinId::DOOR_CLOSED(), EmulatorPinValue::OFF());

        $this->waitUntilMailCatcherHasMessages();
        $this->assertEquals(1, self::$mailCatcher->getMessageCount());

        $message = self::$mailCatcher->searchOne();
        $this->assertRegExp('/^DOOR OPENING/', $message->getSubject());
    }

    /**
     * @test
     * @depends it_should_send_a_mail_on_door_opening
     */
    public function it_should_send_a_mail_on_door_closed_again()
    {
        self::setEmulatorInputPin(EmulatorPinId::DOOR_CLOSED(), EmulatorPinValue::ON());

        $this->waitUntilMailCatcherHasMessages();
        $this->assertEquals(1, self::$mailCatcher->getMessageCount());

        $message = self::$mailCatcher->searchOne();
        $this->assertRegExp('/^Door closed/', $message->getSubject());
    }

    /**
     * @test
     * @depends it_should_send_a_mail_on_door_closed_again
     */
    public function it_should_send_a_mail_on_hardware_error()
    {
        self::setEmulatorInputPin(EmulatorPinId::DOOR_CLOSED(), EmulatorPinValue::ON());
        self::setEmulatorInputPin(EmulatorPinId::DOOR_OPENED(), EmulatorPinValue::ON());

        $this->waitUntilMailCatcherHasMessages();
        $this->assertEquals(1, self::$mailCatcher->getMessageCount());

        $message = self::$mailCatcher->searchOne();
        $this->assertRegExp('/^HARDWARE ERROR/', $message->getSubject());
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        self::$mailCatcher->purge();
    }

    private function waitUntilMailCatcherHasMessages($minNumberToWaitFor = 1, $timeout = 5)
    {
        $start = time();
        while (self::$mailCatcher->getMessageCount() < $minNumberToWaitFor) {
            if (time() > $start + $timeout) {
                $this->fail('MailCatcher did not receive any message within timeout');
            }
            usleep(300000);
        }
    }
}
