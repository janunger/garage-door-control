<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit\ControlSequence\Sequence;

use JUIT\GDC\ControlSequence\Command;
use JUIT\GDC\ControlSequence\Sequence\Factory;
use JUIT\GDC\ControlSequence\Sequence\TriggerDoor;
use JUIT\GDC\Door\DoorInterface;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /** @var Factory */
    private $SUT;

    protected function setUp()
    {
        parent::setUp();
        $this->SUT = new Factory($this->createMock(DoorInterface::class));
    }

    /** @test */
    public function it_creates_a_trigger_door_sequence_for_a_trigger_door_command()
    {
        static::assertInstanceOf(
            TriggerDoor::class,
            $this->SUT->createSequenceFor(Command::TRIGGER_DOOR())
        );
    }
}
