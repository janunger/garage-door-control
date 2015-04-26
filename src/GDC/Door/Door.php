<?php

namespace GDC\Door;

use Pkj\Raspberry\PiFace\InputPin;
use Pkj\Raspberry\PiFace\OutputPin;

class Door implements DoorInterface
{
    /**
     * @var int
     */
    private $motorTriggerTimeoutSeconds;

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

    /**
     * @param InputPin  $sensorClosed
     * @param InputPin  $sensorOpened
     * @param OutputPin $motorTrigger
     * @param int       $motorTriggerTimeoutSeconds
     */
    public function __construct(
        InputPin $sensorClosed,
        InputPin $sensorOpened,
        OutputPin $motorTrigger,
        $motorTriggerTimeoutSeconds = 10
    ) {
        $this->sensorClosed               = $sensorClosed;
        $this->sensorOpened               = $sensorOpened;
        $this->motorTrigger               = $motorTrigger;
        $this->motorTriggerTimeoutSeconds = $motorTriggerTimeoutSeconds;
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
        $this->waitForMotorTriggerToTurnOn();
        usleep(500000);
        $this->motorTrigger->turnOff();
    }

    private function waitForMotorTriggerToTurnOn()
    {
        $start = time();
        while (!$this->motorTrigger->isOn()) {
            usleep(100000);
            if (time() > $start + $this->motorTriggerTimeoutSeconds) {
                throw new HardwareErrorException("Motor trigger didn't turn on within expected timeout");
            }
        }
    }
}
