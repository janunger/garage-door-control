<?php

namespace GDCBundle\Tests\Service\SensorLogger;

use Pkj\Raspberry\PiFace\InputPin;

class InputPinMock implements InputPin
{
    private $isOn = false;

    public function setIsOn($flag)
    {
        $this->isOn = $flag;
    }

    /**
     * @return bool
     */
    public function isOn()
    {
        return $this->isOn;
    }
}