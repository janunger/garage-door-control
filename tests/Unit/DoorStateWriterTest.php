<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit;

use JUIT\GDC\Door\State;
use JUIT\GDC\WatchDog\DoorStateWriter;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class DoorStateWriterTest extends TestCase
{
    /** @var DoorStateWriter */
    private $SUT;

    /** @var \SplFileInfo */
    private $filePath;

    protected function setUp()
    {
        parent::setUp();
        vfsStream::setup('path/to');
        $this->filePath = new \SplFileInfo(vfsStream::url('path/to/state.json'));
        $this->SUT      = new DoorStateWriter($this->filePath);
    }

    /** @test */
    public function it_writes_a_json_file_to_a_given_path()
    {
        static::assertFileNotExists($this->filePath->getPathname());

        $this->SUT->write(State::CLOSED(), new \DateTimeImmutable());

        static::assertFileExists($this->filePath->getPathname());
        static::assertJson(file_get_contents($this->filePath->getPathname()));
    }

    /** @test */
    public function it_writes_door_state_and_date_into_the_json()
    {
        $this->SUT->write(State::CLOSED(), new \DateTimeImmutable('2017-10-23T23:00:00+02:00'));

        static::assertJsonStringEqualsJsonString(
            '{"doorState":"closed","date":"2017-10-23T23:00:00+02:00"}',
            file_get_contents($this->filePath->getPathname())
        );
    }
}
