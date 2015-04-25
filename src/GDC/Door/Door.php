<?php

namespace GDC\Door;

use Pkj\Raspberry\PiFace\InputPin;
use Pkj\Raspberry\PiFace\OutputPin;

class Door implements DoorInterface
{
    /**
     * @var InputPin
     */
    private $sensorClosed;

    /**
     * @var InputPin
     */
    private $sensorOpened;

    /**
     * @var OutputPin
     */
    private $motorTrigger;

    public function __construct(InputPin $sensorClosed, InputPin $sensorOpened, OutputPin $motorTrigger)
    {
        $this->sensorClosed = $sensorClosed;
        $this->sensorOpened = $sensorOpened;
        $this->motorTrigger = $motorTrigger;
    }

    /**
     * @return State
     * @throws HardwareErrorException
     */
    public function getState()
    {
        if ($this->sensorClosed->isOn() && $this->sensorOpened->isOn()) {
            throw new HardwareErrorException('Both sensors are on');
        }

        if ($this->sensorOpened->isOn()) {
            return State::OPENED();
        }

        if ($this->sensorClosed->isOn()) {
            return State::CLOSED();
        }

        return State::UNKNOWN();
    }

    public function triggerControl()
    {
        $this->motorTrigger->turnOn();
        usleep(500000);
        $this->motorTrigger->turnOff();
    }
}
