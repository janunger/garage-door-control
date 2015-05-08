<?php

namespace GDCBundle\Service\AutoSequence;

use GDC\Door\DoorInterface;
use Pkj\Raspberry\PiFace\InputPin;

class CloseAfterOneTransit implements AutoSequence
{
    const PHASE_JUST_STARTED = 'just-started';
    const PHASE_DOOR_OPENING = 'door-opening';

    /**
     * @var InputPin
     */
    private $sensorPhotoInterrupter;

    /**
     * @var DoorInterface
     */
    private $door;

    /**
     * @var string
     */
    private $phase = self::PHASE_JUST_STARTED;

    public function __construct(DoorInterface $door, InputPin $sensorPhotoInterrupter)
    {
        $this->sensorPhotoInterrupter = $sensorPhotoInterrupter;
        $this->door                   = $door;
    }

    /**
     * @return State
     */
    public function tick()
    {
        if (self::PHASE_JUST_STARTED === $this->phase) {
            $this->door->triggerControl();
            $this->phase = self::PHASE_DOOR_OPENING;
        }

        return State::RUNNING();
    }
}