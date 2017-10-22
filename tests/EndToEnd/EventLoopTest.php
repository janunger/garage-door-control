<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\EndToEnd;

use JUIT\MailHog\MailHogClient;
use JUIT\MailHog\Message;
use JUIT\PiFace\Emulator\InputPin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class EventLoopTest extends TestCase
{
    /** @var Process */
    private static $eventLoop;

    /** @var MailHogClient */
    private static $mailHog;

    /** @var InputPin */
    private $pifacePinDoorClosed;

    /** @var InputPin */
    private $pifacePinDoorOpened;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // "exec ..." is required if $process->stop() isn't working without.
        static::$eventLoop = new Process('exec ' . PROJECT_ROOT_DIR . '/bin/event_loop');
        static::$mailHog   = MailHogClient::create('http://mailhog:8025');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->pifacePinDoorClosed = new InputPin(0, new \SplFileInfo(EMULATOR_DATA_DIR));
        $this->pifacePinDoorOpened = new InputPin(1, new \SplFileInfo(EMULATOR_DATA_DIR));

        static::$mailHog->deleteAll();
    }

    /** @test */
    public function it_sends_a_mail_with_door_state_on_startup()
    {
        $this->pifacePinDoorClosed->setOn();
        $this->pifacePinDoorOpened->setOff();

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
        $this->pifacePinDoorClosed->setOff();

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
        $this->pifacePinDoorClosed->setOn();

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
        $this->pifacePinDoorClosed->setOn();
        $this->pifacePinDoorOpened->setOn();

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
        if (self::$eventLoop->isStarted()) {
            self::$eventLoop->stop();
        }
        static::$mailHog->deleteAll();

        parent::tearDownAfterClass();
    }
}
