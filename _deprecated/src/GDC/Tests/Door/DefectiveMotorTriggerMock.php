<?php

namespace GDC\Tests\Door;

use Pkj\Raspberry\PiFace\OutputPin;

class DefectiveMotorTriggerMock implements OutputPin
{
    public function turnOff()
    {}

    public function turnOn()
    {
        // Simulate defect: Don't turn on
    }

    /**
     * @return bool
     */
    public function isOn()
    {
        return false;
    }

    public function toggle()
    {}
}