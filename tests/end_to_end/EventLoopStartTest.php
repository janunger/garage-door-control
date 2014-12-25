<?php

use Alex\MailCatcher\Client;
use Symfony\Component\Process\Process;

class EventLoopStartTest extends PHPUnit_Framework_TestCase
{
    /** @var Process */
    private static $eventLoop;

    /** @var Client */
    private static $mailCatcher;

    public static function setUpBeforeClass()
    {
        // "exec ..." is required if $process->stop() isn't working without.
        self::$eventLoop   = new Process('exec ' . __DIR__ . '/../../app/console gdc:event-loop:run');
        self::$mailCatcher = new Client('http://localhost:1081');
    }

    protected function setUp()
    {
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
        self::$eventLoop->stop();
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
