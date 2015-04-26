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
}
