<?php

use Alex\MailCatcher\Client;

require_once __DIR__ . '/EndToEndTestCase.php';

class EventLoopStartTest extends EndToEndTestCase
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
    public function it_should_send_a_mail_on_start()
    {
        self::$eventLoop->start();

        $this->waitUntilMailCatcherHasMessages();
        $this->assertEquals(1, self::$mailCatcher->getMessageCount());

        $message = self::$mailCatcher->searchOne();
        $this->assertRegExp('/^Watchdog restarted/', $message->getSubject());
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        self::$mailCatcher->purge();
    }

    private function waitUntilMailCatcherHasMessages($timeout = 5)
    {
        $start = time();
        while (self::$mailCatcher->getMessageCount() === 0) {
            if (time() > $start + $timeout) {
                $this->fail('MailCatcher did not receive any message within timeout');
            }
            usleep(300000);
        }
    }
}
