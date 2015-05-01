<?php

namespace GDCBundle\Tests\Event;

use GDC\Door\State;
use GDCBundle\Event\DoorStateChangeEvent;
use GDCBundle\Event\DoorStateListener;
use GDCBundle\Model\Microtime;

class DoorStateListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_call_the_state_writer_with_the_given_data()
    {
        $doorStateWriter = $this
            ->getMockBuilder('\GDCBundle\Service\DoorStateWriter')
            ->disableOriginalConstructor()
            ->getMock();
        $SUT = new DoorStateListener($doorStateWriter);

        $state = State::CLOSED();
        $time = new Microtime();
        $doorStateWriter->expects($this->once())->method('write')->with($state, $time);

        $SUT->onDoorStateChange(new DoorStateChangeEvent($state, $time));
    }
}
