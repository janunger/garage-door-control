<?php

namespace GDCBundle\Tests\Service\AutoSequence;

use GDC\Tests\AbstractTestCase;
use GDCBundle\Service\AutoSequence\State;

class StateTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_should_equal_to_other_instance_with_same_value()
    {
        $SUT = State::RUNNING();

        $this->assertTrue($SUT->equals(State::RUNNING()));
    }

    /**
     * @test
     */
    public function it_should_not_equal_to_other_instance_with_other_value()
    {
        $SUT = State::RUNNING();

        $this->assertFalse($SUT->equals(State::FINISHED()));
    }

    /**
     * @test
     */
    public function it_should_not_equal_to_null()
    {
        $SUT = State::RUNNING();

        $this->assertFalse($SUT->equals(null));
    }
}