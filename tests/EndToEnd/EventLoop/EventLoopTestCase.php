<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\EndToEnd\EventLoop;

use JUIT\PiFace\Emulator\InputPin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class EventLoopTestCase extends TestCase
{
    /** @var Process */
    protected static $eventLoop;

    /** @var InputPin */
    protected $pifacePinDoorOpened;

    /** @var InputPin */
    protected $pifacePinDoorClosed;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // "exec ..." is required if $process->stop() isn't working without.
        static::$eventLoop = new Process('exec ' . PROJECT_ROOT_DIR . '/bin/event_loop');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->pifacePinDoorClosed = new InputPin(0, new \SplFileInfo(EMULATOR_DATA_DIR));
        $this->pifacePinDoorOpened = new InputPin(1, new \SplFileInfo(EMULATOR_DATA_DIR));
    }

    public static function tearDownAfterClass()
    {
        if (self::$eventLoop->isStarted()) {
            self::$eventLoop->stop();
        }

        parent::tearDownAfterClass();
    }
}
