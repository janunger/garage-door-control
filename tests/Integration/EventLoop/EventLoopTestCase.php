<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Integration\EventLoop;

use JUIT\PiFace\Emulator\PiFace;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class EventLoopTestCase extends TestCase
{
    /** @var Process */
    protected static $eventLoop;

    /** @var PiFace */
    protected $piFace;

    /** @var int */
    protected $inputPinIdDoorClosed = 0;

    /** @var int */
    protected $inputPinIdDoorOpened = 1;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // "exec ..." is required if $process->stop() isn't working without.
        static::$eventLoop = new Process('exec ' . PROJECT_ROOT_DIR . '/bin/event_loop');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->piFace = new PiFace(new \SplFileInfo(EMULATOR_DATA_DIR . '/emulator'), 8);
    }

    public static function tearDownAfterClass()
    {
        if (self::$eventLoop->isStarted()) {
            self::$eventLoop->stop();
        }

        parent::tearDownAfterClass();
    }
}
