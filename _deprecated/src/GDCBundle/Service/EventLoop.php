<?php

namespace GDCBundle\Service;

use GDC\WatchDog\WatchDog;
use GDCBundle\Service\AutoSequence\Worker;
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
     * @var Worker
     */
    private $autoSequenceWorker;

    /**
     * @var SensorLogger
     */
    private $sensorLogger;

    public function __construct(
        CommandProcessor $commandProcessor,
        WatchDog $watchDog,
        Worker $autoSequenceWorker,
        SensorLogger $sensorLogger
    ) {
        $this->commandProcessor   = $commandProcessor;
        $this->watchDog           = $watchDog;
        $this->autoSequenceWorker = $autoSequenceWorker;
        $this->sensorLogger       = $sensorLogger;
    }

    public function tick()
    {
        $this->commandProcessor->execute();
        // TODO: $this->hardwareButtonReader->execute();
        // TODO: Rename watchDog to doorSensorReader
        $this->watchDog->execute();
        $this->autoSequenceWorker->tick();
        // TODO: $this->statusDisplay->tick();
        $this->sensorLogger->execute();
    }
}
