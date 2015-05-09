<?php

namespace GDCBundle\Tests\Service\AutoSequence;

use GDC\Tests\AbstractTestCase;
use GDCBundle\Model\AutoSequenceName;
use GDCBundle\Service\AutoSequence\CloseAfterOneTransit;
use GDCBundle\Tests\Service\SensorLogger\InputPinMock;

class CloseAfterOneTransitTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_should_tell_its_name()
    {
        $SUT = new CloseAfterOneTransit(new DoorMock(), new InputPinMock());

        $this->assertEquals(new AutoSequenceName('close-after-one-transit'), $SUT->getName());
    }
}