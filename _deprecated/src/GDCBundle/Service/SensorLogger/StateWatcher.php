<?php

namespace GDCBundle\Service\SensorLogger;

use GDC\Sensor\Role;
use Pkj\Raspberry\PiFace\InputPin;

class StateWatcher
{
    /**
     * @var InputPin
     */
    private $inputPin;

    /**
     * @var Role
     */
    private $role;

    /**
     * @var bool|null
     */
    private $wasOn = null;

    public function __construct(InputPin $inputPin, Role $role)
    {
        $this->inputPin = $inputPin;
        $this->role = $role;
    }

    public function saveState()
    {
        $this->wasOn = $this->inputPin->isOn();
    }

    /**
     * @return bool
     */
    public function isOn()
    {
        return $this->inputPin->isOn();
    }

    /**
     * @return bool
     */
    public function wasOn()
    {
        return $this->wasOn;
    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }
}