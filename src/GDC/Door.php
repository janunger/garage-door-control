<?php

namespace GDC;

use GDC\Door\HardwareErrorException;
use Pkj\Raspberry\PiFace;

class Door
{
    const STATE_OPENED = 'opened';
    const STATE_CLOSED = 'closed';
    const STATE_UNKNOWN = 'unknown';

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
     * @return string
     * @throws Door\HardwareErrorException
     */
    public function getState()
    {
        if ($this->sensorClosed->isOn() && $this->sensorOpened->isOn()) {
            throw new HardwareErrorException('Both sensors are on');
        }

        if ($this->sensorOpened->isOn()) {
            return self::STATE_OPENED;
        }

        if ($this->sensorClosed->isOn()) {
            return self::STATE_CLOSED;
        }

        return self::STATE_UNKNOWN;
    }

    public function triggerControl()
    {
        $this->actorControl->turnOn();
        usleep(500000);
        $this->actorControl->turnOff();
    }
}
