<?php

use MyCLabs\Enum\Enum;
use Pkj\Raspberry\PiFace\Emulator\StateProvider;
use Symfony\Component\Process\Process;

/**
 * @method static EmulatorPinId DOOR_CLOSED()
 * @method static EmulatorPinId DOOR_OPENED()
 */
class EmulatorPinId extends Enum
{
    const DOOR_CLOSED = 0;
    const DOOR_OPENED = 1;
}

/**
 * @method static EmulatorPinValue OFF()
 * @method static EmulatorPinValue ON()
 */
class EmulatorPinValue extends Enum
{
    const OFF = 0;
    const ON = 1;
}

class EventLoopTestCase extends PHPUnit_Framework_TestCase
{
    const PIN_VALUE_ON = 1;
    const PIN_VALUE_OFF = 0;

    /** @var Process */
    protected static $eventLoop;

    /**
     * @var StateProvider
     */
    protected static $piFaceStateProvider;

    public static function setUpBeforeClass()
    {
        // "exec ..." is required if $process->stop() isn't working without.
        self::$eventLoop = new Process('exec ' . __DIR__ . '/../../app/console gdc:event-loop:run');

        self::$piFaceStateProvider = new StateProvider(new SplFileInfo(__DIR__ . '/../../app/var/emulator'));
        self::setEmulatorInputPin(EmulatorPinId::DOOR_CLOSED(), EmulatorPinValue::ON());
        self::setEmulatorInputPin(EmulatorPinId::DOOR_OPENED(), EmulatorPinValue::OFF());
    }

    public static function tearDownAfterClass()
    {
        if (self::$eventLoop->isStarted()) {
            self::$eventLoop->stop();
        }
    }

    protected static function setEmulatorInputPin(EmulatorPinId $id, EmulatorPinValue $value)
    {
        self::$piFaceStateProvider->writeInput($id->getValue(), 0, $value->getValue());
        // Avoid timing problems due to file system
        usleep(100);
    }
}