<?php

namespace GDCBundle\Tests\Service;

use GDCBundle\Model\Microtime;
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
        $this->phpMock->expects($this->at(0))->method('microtime')->with(true)->willReturn('1430940001.0000');
        $this->phpMock->expects($this->at(1))->method('microtime')->with(true)->willReturn('1430940002.0000');
        $this->phpMock->expects($this->exactly(2))->method('microtime');

        $this->assertEquals(new Microtime('1430940001.0000'), TimeProvider::microtime());
        $this->assertEquals(new Microtime('1430940002.0000'), TimeProvider::microtime());
    }

    /**
     * @test
     */
    public function it_should_return_a_static_mock_if_given()
    {
        $this->phpMock->expects($this->never())->method('microtime');

        TimeProvider::setTestMicrotime(new Microtime('1430940000.0000'));

        $this->assertEquals(new Microtime('1430940000.0000'), TimeProvider::microtime());
    }
}