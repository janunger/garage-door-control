<?php

namespace GDC\Tests\Door;

use GDC\Door\State;
use PHPUnit_Framework_TestCase;

class StateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_equal_to_other_instance_with_same_value()
    {
        $SUT = State::CLOSED();

        $this->assertTrue($SUT->equals(State::CLOSED()));
    }

    /**
     * @test
     */
    public function it_should_not_equal_to_other_instance_with_other_value()
    {
        $SUT = State::CLOSED();

        $this->assertFalse($SUT->equals(State::OPENED()));
    }

    /**
     * @test
     */
    public function it_should_not_equal_to_null()
    {
        $SUT = State::CLOSED();

        $this->assertFalse($SUT->equals(null));
    }
}
