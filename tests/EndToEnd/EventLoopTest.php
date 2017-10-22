<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\EndToEnd;

use JUIT\MailHog\MailHogClient;
use JUIT\PiFace\Emulator\InputPin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class EventLoopTest extends TestCase
{
    /** @var Process */
    private static $eventLoop;

    /** @var MailHogClient */
    private $mailHog;

    /** @var InputPin */
    private $pifacePinDoorClosed;

    /** @var InputPin */
    private $pifacePinDoorOpened;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // "exec ..." is required if $process->stop() isn't working without.
        static::$eventLoop = new Process('exec ');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->mailHog = MailHogClient::create('http://mailhog:8025');
        $this->pifacePinDoorClosed = new InputPin(0, new \SplFileInfo(EMULATOR_DATA_DIR));
        $this->pifacePinDoorOpened = new InputPin(1, new \SplFileInfo(EMULATOR_DATA_DIR));

        $this->mailHog->deleteAll();
    }

    /** @test */
    public function it_should_send_a_mail_with_door_state_on_startup()
    {
        $this->markTestIncomplete();

        $this->pifacePinDoorClosed->setOn();
        $this->pifacePinDoorOpened->setOff();

//        static::$eventLoop->start();

        $messages = $this->mailHog->fetchAll();
        static::assertCount(1, $messages);
    }

    public static function tearDownAfterClass()
    {
        if (self::$eventLoop->isStarted()) {
            self::$eventLoop->stop();
        }

        parent::tearDownAfterClass();
    }
}
