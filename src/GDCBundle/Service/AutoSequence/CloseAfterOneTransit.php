<?php

namespace GDCBundle\Service\AutoSequence;

use GDC\Door\DoorInterface;
use GDC\Door\State as DoorState;
use Pkj\Raspberry\PiFace\InputPin;

class CloseAfterOneTransit implements AutoSequence
{
    const PHASE_JUST_STARTED = 'just-started';
    const PHASE_DOOR_OPENING = 'door-opening';
    const PHASE_DOOR_OPENED = 'door-opened';
    const PHASE_WAITING_FOR_PHOTO_INTERRUPTER_TO_SWITCH_OFF = 'waiting-for-photo-interrupter-to-switch-off';
    const PHASE_FINISHED = 'finished';

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
        if (self::PHASE_FINISHED === $this->phase) {
            return State::FINISHED();
        }
        if (self::PHASE_JUST_STARTED === $this->phase) {
            $this->phase = self::PHASE_DOOR_OPENING;
            $this->door->triggerControl();
        }
        if (self::PHASE_DOOR_OPENING === $this->phase) {
            if ($this->door->getState()->equals(DoorState::OPENED())) {
                $this->phase = self::PHASE_DOOR_OPENED;
            }
        }
        if (self::PHASE_DOOR_OPENED === $this->phase) {
            if ($this->sensorPhotoInterrupter->isOn()) {
                $this->phase = self::PHASE_WAITING_FOR_PHOTO_INTERRUPTER_TO_SWITCH_OFF;
            }
        }
        if (self::PHASE_WAITING_FOR_PHOTO_INTERRUPTER_TO_SWITCH_OFF === $this->phase) {
            if (!$this->sensorPhotoInterrupter->isOn()) {
                $this->phase = self::PHASE_FINISHED;
                $this->door->triggerControl();

                return State::FINISHED();
            }
        }

        return State::RUNNING();
    }
}