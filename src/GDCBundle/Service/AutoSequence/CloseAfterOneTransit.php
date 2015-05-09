<?php

namespace GDCBundle\Service\AutoSequence;

use GDC\Door\DoorInterface;
use GDC\Door\State as DoorState;
use GDCBundle\Model\AutoSequenceName;
use GDCBundle\Service\TimeProvider;
use Pkj\Raspberry\PiFace\InputPin;

class CloseAfterOneTransit implements AutoSequence
{
    const DOOR_JUST_STARTED = 'DOOR_JUST_STARTED';
    const DOOR_OPENING = 'DOOR_OPENING';
    const DOOR_OPENED = 'DOOR_OPENED';
    const DOOR_FINISHED = 'DOOR_FINISHED';

    const PHOTO_INTERRUPTER_NOT_TRIGGERED = 'PHOTO_INTERRUPTER_NOT_TRIGGERED';
    const PHOTO_INTERRUPTER_WENT_ON = 'PHOTO_INTERRUPTER_WENT_ON';
    const PHOTO_INTERRUPTER_WENT_OFF_AGAIN = 'PHOTO_INTERRUPTER_WENT_OFF_AGAIN';
    const NAME = 'close-after-one-transit';

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
    private $doorPhase;

    /**
     * @var string
     */
    private $photoInterrupterPhase = self::PHOTO_INTERRUPTER_NOT_TRIGGERED;

    /**
     * @var float
     */
    private $startTime = null;

    /**
     * @var float
     */
    private $doorOpenedTime = null;
    
    public function __construct(DoorInterface $door, InputPin $sensorPhotoInterrupter)
    {
        $this->sensorPhotoInterrupter = $sensorPhotoInterrupter;
        $this->door                   = $door;
        $this->init();
    }

    private function init()
    {
        $this->startTime = TimeProvider::microtime();

        if ($this->door->getState()->equals(DoorState::CLOSED())) {
            $this->doorPhase = self::DOOR_JUST_STARTED;
        }
        if ($this->door->getState()->equals(DoorState::UNKNOWN())) {
            $this->doorPhase = self::DOOR_OPENING;
        }
        if ($this->door->getState()->equals(DoorState::OPENED())) {
            $this->doorPhase = self::DOOR_OPENED;
            $this->doorOpenedTime = TimeProvider::microtime();
        }
    }

    /**
     * @return State
     */
    public function tick()
    {
        if ($this->isSequenceFinished()) {
            return State::FINISHED();
        }

        $this->updatePhotoInterrupter();

        if (self::DOOR_JUST_STARTED === $this->doorPhase) {
            $this->doorPhase = self::DOOR_OPENING;
            $this->door->triggerControl();
        }
        if (self::DOOR_OPENING === $this->doorPhase) {
            if ($this->door->getState()->equals(DoorState::OPENED())) {
                $this->doorPhase = self::DOOR_OPENED;
            }
        }
        if (self::DOOR_OPENED === $this->doorPhase) {
            if (null === $this->doorOpenedTime) {
                $this->doorOpenedTime = TimeProvider::microtime();
            }
            if ($this->isTargetReached() && $this->isDoorOpenedLongEnough()) {
                $this->doorPhase = self::DOOR_FINISHED;
                $this->door->triggerControl();

                return State::FINISHED();
            }
        }

        return State::RUNNING();
    }

    private function updatePhotoInterrupter()
    {
        if (!$this->mustUpdatePhotoInterrupter()) {
            return;
        }

        if (self::PHOTO_INTERRUPTER_NOT_TRIGGERED === $this->photoInterrupterPhase) {
            if ($this->sensorPhotoInterrupter->isOn()) {
                $this->photoInterrupterPhase = self::PHOTO_INTERRUPTER_WENT_ON;
            }
        }
        if (self::PHOTO_INTERRUPTER_WENT_ON === $this->photoInterrupterPhase) {
            if (!$this->sensorPhotoInterrupter->isOn()) {
                $this->photoInterrupterPhase = self::PHOTO_INTERRUPTER_WENT_OFF_AGAIN;
            }
        }
    }

    /**
     * @return bool
     */
    private function mustUpdatePhotoInterrupter()
    {
        if (null === $this->startTime) {
            return false;
        }
        if ($this->isTooEarlyForPhotoInterrupter()) {
            return false;
        }
        if (self::DOOR_OPENING === $this->doorPhase) {
            return true;
        }
        if (self::DOOR_OPENED === $this->doorPhase) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    private function isSequenceFinished()
    {
        return self::DOOR_FINISHED === $this->doorPhase;
    }

    /**
     * @return bool
     */
    private function isTargetReached()
    {
        return self::PHOTO_INTERRUPTER_WENT_OFF_AGAIN === $this->photoInterrupterPhase;
    }

    /**
     * @return bool
     */
    private function isTooEarlyForPhotoInterrupter()
    {
        if (self::DOOR_OPENED === $this->doorPhase) {
            return false;
        }

        $threshold = '7.0';
        $now  = TimeProvider::microtime();
        $diff = bcsub($now, $this->startTime, 3);

        return 1 === bccomp($threshold, $diff, 3);
    }

    /**
     * @return bool
     */
    private function isDoorOpenedLongEnough()
    {
        if (null === $this->doorOpenedTime) {
            return false;
        }
        $threshold = '1.0';
        $now  = TimeProvider::microtime();
        $diff = bcsub($now, $this->doorOpenedTime, 3);

        return -1 === bccomp($threshold, $diff, 3);
    }

    /**
     * @return AutoSequenceName
     */
    public function getName()
    {
        return new AutoSequenceName(self::NAME);
    }
}