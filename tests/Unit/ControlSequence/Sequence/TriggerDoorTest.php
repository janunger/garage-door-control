<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit\ControlSequence\Sequence;

use JUIT\GDC\ControlSequence\Sequence\State;
use JUIT\GDC\ControlSequence\Sequence\TriggerDoor;
use JUIT\GDC\Tests\Unit\ControlSequence\DoorMock;
use PHPUnit\Framework\TestCase;

class TriggerDoorTest extends TestCase
{
    /** @test */
    public function it_triggers_the_door_control_immediately_and_finish()
    {
        $door = new DoorMock();
        $SUT = new TriggerDoor($door);
        static::assertEquals(0, $door->getTriggerControlCount());

        $state = $SUT->tick();

        static::assertEquals(State::FINISHED(), $state);
        static::assertEquals(1, $door->getTriggerControlCount());
    }
}
