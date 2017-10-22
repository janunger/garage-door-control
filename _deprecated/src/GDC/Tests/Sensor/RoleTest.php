<?php

namespace GDC\Tests\Sensor;

use GDC\Sensor\Role;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_equal_to_itself()
    {
        $SUT = Role::DOOR_CLOSED();
        $this->assertTrue($SUT->equals($SUT));
    }

    /**
     * @test
     */
    public function it_should_equal_to_other_instance_with_same_value()
    {
        $SUT = Role::DOOR_CLOSED();
        $this->assertTrue($SUT->equals(Role::DOOR_CLOSED()));
    }

    /**
     * @test
     */
    public function it_should_not_equal_to_other_instance_with_other_value()
    {
        $SUT = Role::DOOR_CLOSED();
        $this->assertFalse($SUT->equals(Role::DOOR_OPENED()));
    }
}