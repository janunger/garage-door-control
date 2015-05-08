<?php

namespace GDCBundle\Tests\Service;

use GDCBundle\Service\TimeProvider;

class TimeProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $phpMock;

    protected function setUp()
    {
        $this->phpMock = \PHPUnit_Extension_FunctionMocker::start($this, 'GDCBundle\Service')
            ->mockFunction('microtime')
            ->getMock();
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function it_should_return_the_current_microtime()
    {
        $this->phpMock->expects($this->at(0))->method('microtime')->willReturn('0.00000000 1430940001');
        $this->phpMock->expects($this->at(1))->method('microtime')->willReturn('0.00000000 1430940002');
        $this->phpMock->expects($this->exactly(2))->method('microtime');

        $this->assertEquals('0.00000000 1430940001', TimeProvider::microtime());
        $this->assertEquals('0.00000000 1430940002', TimeProvider::microtime());
    }

    /**
     * @test
     */
    public function it_should_return_a_static_mock_if_given()
    {
        $this->phpMock->expects($this->never())->method('microtime');

        TimeProvider::setTestMicrotime('0.00000000 1430940000');

        $this->assertEquals('0.00000000 1430940000', TimeProvider::microtime());
    }
}