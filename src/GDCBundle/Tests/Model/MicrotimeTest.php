<?php

namespace GDCBundle\Tests\Model;

use GDC\Tests\AbstractTestCase;
use GDCBundle\Model\Microtime;

class MicrotimeTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_should_concatenate_a_microtime_value_to_a_bigint_string_representation()
    {
        $SUT = new Microtime('0.78396600 1430048647');

        $this->assertEquals('143004864778396600', $SUT->getValue());
    }

    /**
     * @test
     */
    public function it_should_accept_a_bigint_string_representation_as_value()
    {
        $SUT = new Microtime('143004864778396600');

        $this->assertEquals('143004864778396600', $SUT->getValue());
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception_on_unexpected_input()
    {
        $this->setExpectedException('InvalidArgumentException', "Unexpected microtime representation '1430048647.7839'");
        new Microtime(1430048647.7839);
    }

    /**
     * @test
     */
    public function it_should_return_the_integer_part()
    {
        $SUT = new Microtime('143004864778396600');
        $this->assertSame('1430048647', $SUT->getIntegerPart());

        $SUT = new Microtime('1143004864778396600');
        $this->assertSame('11430048647', $SUT->getIntegerPart());
    }

    /**
     * @test
     */
    public function it_should_return_the_decimal_part()
    {
        $SUT = new Microtime('143004864778396600');
        $this->assertSame('78396600', $SUT->getDecimalPart());

        $SUT = new Microtime('1143004864778396600');
        $this->assertSame('78396600', $SUT->getDecimalPart());
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
        $phpMock->expects($this->once())->method('microtime')->willReturn('0.43932300 1430479381');

        $SUT = new Microtime();

        $this->assertEquals('143047938143932300', $SUT->getValue());
    }

    /**
     * @test
     */
    public function it_should_calculate_the_difference_from_other_instance()
    {
        $SUT = new Microtime('0.78396600 1430048647');
        $this->assertEquals(new Microtime('0.00000000 2'), $SUT->subtract(new Microtime('0.78396600 1430048645')));
    }

    /**
     * @test
     */
    public function it_should_tell_if_it_s_less_than_other_instance()
    {
        $SUT = new Microtime('0.10000000 1430040001');

        $this->assertTrue($SUT->isGreaterThan(new Microtime('0.00000000 1430040000')));
        $this->assertTrue($SUT->isGreaterThan(new Microtime('0.10000000 1430040000')));
        $this->assertTrue($SUT->isGreaterThan(new Microtime('0.00000000 1430040001')));
        $this->assertFalse($SUT->isGreaterThan(new Microtime('0.10000000 1430040001')));
        $this->assertFalse($SUT->isGreaterThan(new Microtime('0.20000000 1430040001')));
        $this->assertFalse($SUT->isGreaterThan(new Microtime('0.00000000 1430040002')));
    }
}
