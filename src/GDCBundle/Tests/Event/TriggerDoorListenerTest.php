<?php

namespace GDCBundle\Tests\Event;

use GDC\CommandQueue\Command;
use GDC\Tests\AbstractTestCase;
use GDCBundle\Event\CommandIssuedEvent;
use GDCBundle\Event\TriggerDoorListener;

class TriggerDoorListenerTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_should_trigger_the_door_on_a_trigger_door_command()
    {
        $door = $this->createMock('GDC\Door\DoorInterface');
        $door->expects($this->once())->method('triggerControl');

        $SUT = new TriggerDoorListener($door);

        $SUT->onCommandIssued(new CommandIssuedEvent(Command::TRIGGER_DOOR()));
    }
}
