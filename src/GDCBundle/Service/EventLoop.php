<?php

namespace GDCBundle\Service;

use GDC\WatchDog\WatchDog;
use GDCBundle\Service\SensorLogger\SensorLogger;

class EventLoop
{
    /**
     * @var CommandProcessor
     */
    private $commandProcessor;

    /**
     * @var WatchDog
     */
    private $watchDog;

    /**
     * @var SensorLogger
     */
    private $sensorLogger;

    public function __construct(CommandProcessor $commandProcessor, WatchDog $watchDog, SensorLogger $sensorLogger)
    {
        $this->commandProcessor = $commandProcessor;
        $this->watchDog         = $watchDog;
        $this->sensorLogger     = $sensorLogger;
    }

    public function tick()
    {
        $this->commandProcessor->execute();
        $this->watchDog->execute();
        $this->sensorLogger->execute();
    }
}