<?php

use Symfony\Component\Process\Process;

require_once __DIR__ . '/EndToEndTestCase.php';

class EndToEndTestCase extends PHPUnit_Framework_TestCase
{
    /** @var Process */
    protected static $eventLoop;

    public static function setUpBeforeClass()
    {
        // "exec ..." is required if $process->stop() isn't working without.
        self::$eventLoop = new Process('exec ' . __DIR__ . '/../../app/console gdc:event-loop:run');
    }

    public static function tearDownAfterClass()
    {
        if (self::$eventLoop->isStarted()) {
            self::$eventLoop->stop();
        }
    }
}