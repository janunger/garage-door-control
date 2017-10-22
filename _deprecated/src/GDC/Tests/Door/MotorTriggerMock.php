<?php

namespace GDC\Tests\Door;

use Pkj\Raspberry\PiFace\OutputPin;

class MotorTriggerMock implements OutputPin
{
    /** @var bool */
    private $isOn = false;

    /**
     * @var array
     */
    private $events = [];

    public function turnOff()
    {
        $this->isOn = false;
        $this->events[] = ['microtime' => microtime(true), 'action' => 'turnOff'];
    }

    public function turnOn()
    {
        $this->isOn = true;
        $this->events[] = ['microtime' => microtime(true), 'action' => 'turnOn'];
    }

    /**
     * @return bool
     */
    public function isOn()
    {
        return $this->isOn;
    }

    public function toggle()
    {
        $this->isOn = !$this->isOn;
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }
}