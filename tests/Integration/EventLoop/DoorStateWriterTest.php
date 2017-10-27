<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Integration\EventLoop;

use JUIT\GDC\Door\State;

class DoorStateWriterTest extends EventLoopTestCase
{
    /** @test */
    public function it_writes_the_door_state()
    {
        $filePath = PROJECT_ROOT_DIR . '/public/state/current.json';
        $this->piFace->setPinOn($this->inputPinIdDoorClosed);
        $this->piFace->setPinOff($this->inputPinIdDoorOpened);
        $before = (new \DateTimeImmutable())->modify('-3 seconds');

        static::$eventLoop->start();
        sleep(1);

        static::assertFileExists($filePath);
        $fileContents = file_get_contents($filePath);
        static::assertJson($fileContents);
        $actual = json_decode($fileContents);
        static::assertSame(State::CLOSED, $actual->doorState);

        $actualDate = new \DateTimeImmutable($actual->date);
        static::assertGreaterThanOrEqual($before, $actualDate);
        $after = (new \DateTimeImmutable())->modify('+3 seconds');
        static::assertLessThanOrEqual($after, $actualDate);
    }
}
