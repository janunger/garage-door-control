<?php

namespace GDCBundle\Service\SensorLogger;

use GDC\Sensor\Role;
use GDCBundle\Entity\SensorLogEntryRepository;
use Pkj\Raspberry\PiFace\InputPin;

class Factory
{
    /**
     * @var SensorLogEntryRepository
     */
    private $logEntryRepository;

    /**
     * @var InputPin
     */
    private $sensorDoorClosed;

    /**
     * @var InputPin
     */
    private $sensorDoorOpened;

    /**
     * @var InputPin
     */
    private $photoInterrupter;

    public function __construct(
        SensorLogEntryRepository $logEntryRepository,
        InputPin $sensorDoorClosed,
        InputPin $sensorDoorOpened,
        InputPin $photoInterrupter
    ) {
        $this->logEntryRepository = $logEntryRepository;
        $this->sensorDoorClosed   = $sensorDoorClosed;
        $this->sensorDoorOpened   = $sensorDoorOpened;
        $this->photoInterrupter   = $photoInterrupter;
    }

    /**
     * @return SensorLogger
     */
    public function createInstance()
    {
        return new SensorLogger($this->logEntryRepository, [
            new StateWatcher($this->sensorDoorClosed, Role::DOOR_CLOSED()),
            new StateWatcher($this->sensorDoorOpened, Role::DOOR_OPENED()),
            new StateWatcher($this->photoInterrupter, Role::PHOTO_INTERRUPTER())
        ]);
    }
}