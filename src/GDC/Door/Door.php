<?php

namespace GDC\Door;

use Pkj\Raspberry\PiFace;

class Door implements DoorInterface
{
    /**
     * @var \Pkj\Raspberry\PiFace\InputPin
     */
    private $sensorClosed;

    /**
     * @var \Pkj\Raspberry\PiFace\InputPin
     */
    private $sensorOpened;

    /**
     * @var \Pkj\Raspberry\PiFace\OutputPin
     */
    private $actorControl;

    public function __construct(PiFace $piFace)
    {
        $this->sensorClosed = $piFace->getInputPin(0);
        $this->sensorOpened = $piFace->getInputPin(1);
        $this->actorControl = $piFace->getOutputPin(0);
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
        $this->actorControl->turnOn();
        usleep(500000);
        $this->actorControl->turnOff();
    }
}
