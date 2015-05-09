<?php

namespace GDCBundle\Tests\Model;

use GDC\Tests\AbstractTestCase;
use GDCBundle\Model\Microtime;

class MicrotimeTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_should_accept_a_float_as_value()
    {
        $SUT = new Microtime(1431203854.7798);

        $this->assertSame('1431203854.7798', $SUT->getValue());
    }

    /**
     * @test
     */
    public function it_should_accept_a_string_representation_as_value()
    {
        $SUT = new Microtime('1431203854.7798');

        $this->assertSame('1431203854.7798', $SUT->getValue());
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception_on_unexpected_input()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            "Unexpected microtime representation '0.02947100 1431203894'"
        );
        new Microtime('0.02947100 1431203894');
    }

    /**
     * @test
     */
    public function it_should_return_the_integer_part()
    {
        $SUT = new Microtime('1431203854.7798');
        $this->assertSame('1431203854', $SUT->getIntegerPart());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function it_should_accept_an_empty_construction_parameter_and_assume_current_microtime_as_value()
    {
        $phpMock = \PHPUnit_Extension_FunctionMocker::start($this, 'GDCBundle\Model')
            ->mockFunction('microtime')
            ->getMock();
        $phpMock->expects($this->once())->method('microtime')->with(true)->willReturn(1431203969.0951);

        $SUT = new Microtime();

        $this->assertSame('1431203969.0951', $SUT->getValue());
    }

    /**
     * @test
     */
    public function it_should_calculate_the_difference_from_other_instance()
    {
        $SUT = new Microtime('1430040000.0000');

        $this->assertEquals(new Microtime('0.0'), $SUT->subtract(new Microtime('1430040000.0000')));
        $this->assertEquals(new Microtime('1.0'), $SUT->subtract(new Microtime('1430039999.0000')));
        $this->assertEquals(new Microtime('-0.1'), $SUT->subtract(new Microtime('1430040000.1000')));
    }

    /**
     * @test
     */
    public function it_should_tell_if_it_s_less_than_other_instance()
    {
        $SUT = new Microtime('1430040001.1000');

        $this->assertTrue($SUT->isGreaterThan(new Microtime('1430040000.0000')));
        $this->assertTrue($SUT->isGreaterThan(new Microtime('1430040000.1000')));
        $this->assertTrue($SUT->isGreaterThan(new Microtime('1430040001.0000')));
        $this->assertFalse($SUT->isGreaterThan(new Microtime('1430040001.1000')));
        $this->assertFalse($SUT->isGreaterThan(new Microtime('1430040001.2000')));
        $this->assertFalse($SUT->isGreaterThan(new Microtime('1430040002.0000')));
    }
}
