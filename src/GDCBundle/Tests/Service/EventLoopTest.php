<?php

namespace GDCBundle\Tests\Service;

use GDC\Tests\AbstractTestCase;
use GDC\WatchDog\WatchDog;
use GDCBundle\Service\CommandProcessor;
use GDCBundle\Service\EventLoop;
use GDCBundle\Service\SensorLogger\SensorLogger;

class EventLoopTest extends AbstractTestCase
{
    /**
     * @var EventLoop
     */
    private $SUT;

    /**
     * @var CommandProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commandProcessor;

    /**
     * @var WatchDog|\PHPUnit_Framework_MockObject_MockObject
     */
    private $watchDog;

    /**
     * @var SensorLogger|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sensorLogger;

    protected function setUp()
    {
        $this->commandProcessor = $this->createMock('GDCBundle\Service\CommandProcessor');
        $this->watchDog = $this->createMock('GDC\WatchDog\WatchDog');
        $this->sensorLogger = $this->createMock('GDCBundle\Service\SensorLogger\SensorLogger');

        $this->SUT = new EventLoop($this->commandProcessor, $this->watchDog, $this->sensorLogger);
    }


    /**
     * @test
     */
    public function it_should_trigger_the_loop_services_once_a_tick()
    {
        $this->commandProcessor->expects($this->once())->method('execute');
        $this->watchDog->expects($this->once())->method('execute');
        $this->sensorLogger->expects($this->once())->method('execute');

        $this->SUT->tick();
    }
}