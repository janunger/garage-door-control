<?php

declare(strict_types=1);

namespace JUIT\GDC\Door;

use JUIT\PiFace\InputPin;
use JUIT\PiFace\OutputPin;

class Door
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
    private $actorMotor;

    public function __construct(InputPin $sensorClosed, InputPin $sensorOpened, OutputPin $actorMotor)
    {
        $this->sensorClosed = $sensorClosed;
        $this->sensorOpened = $sensorOpened;
        $this->actorMotor   = $actorMotor;
    }

    public function getState(): State
    {
        $isClosed = $this->sensorClosed->isOn();
        $isOpened = $this->sensorOpened->isOn();

        if ($isClosed && $isOpened) {
            throw new HardwareError();
        }

        if ($isClosed) {
            return State::CLOSED();
        }
        if ($isOpened) {
            return State::OPENED();
        }

        return State::UNKNOWN();
    }

    public function triggerControl()
    {
        $this->actorMotor->trigger(500);
    }
}
