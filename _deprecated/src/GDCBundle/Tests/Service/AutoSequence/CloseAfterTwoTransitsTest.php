<?php

namespace GDCBundle\Tests\Service\AutoSequence;

use GDC\Tests\AbstractTestCase;
use GDCBundle\Model\AutoSequenceName;
use GDCBundle\Service\AutoSequence\CloseAfterTwoTransits;
use GDCBundle\Tests\Service\SensorLogger\InputPinMock;

class CloseAfterTwoTransitsTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_should_tell_its_name()
    {
        $SUT = new CloseAfterTwoTransits(new DoorMock(), new InputPinMock());

        $this->assertEquals(new AutoSequenceName('close-after-two-transits'), $SUT->getName());
    }
}