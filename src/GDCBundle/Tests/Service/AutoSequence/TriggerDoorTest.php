<?php

namespace GDCBundle\Tests\Service\AutoSequence;

use GDC\Tests\AbstractTestCase;
use GDCBundle\Service\AutoSequence\State;
use GDCBundle\Service\AutoSequence\TriggerDoor;

class TriggerDoorTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_should_trigger_the_door_control_immediately_and_finish()
    {
        $door = new DoorMock();
        $SUT = new TriggerDoor($door);
        $this->assertEquals(0, $door->getTriggerControlCount());

        $state = $SUT->tick();

        $this->assertEquals(State::FINISHED(), $state);
        $this->assertEquals(1, $door->getTriggerControlCount());
    }
}