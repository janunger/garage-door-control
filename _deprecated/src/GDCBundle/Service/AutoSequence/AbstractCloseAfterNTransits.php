<?php

namespace GDCBundle\Service\AutoSequence;

use GDC\Door\DoorInterface;
use GDC\Door\State as DoorState;
use GDCBundle\Model\AutoSequenceName;
use GDCBundle\Model\Microtime;
use GDCBundle\Service\TimeProvider;
use Pkj\Raspberry\PiFace\InputPin;

abstract class AbstractCloseAfterNTransits implements AutoSequence
{
    const DOOR_JUST_STARTED = 'DOOR_JUST_STARTED';
    const DOOR_WAS_TRIGGERED_TO_OPEN = 'DOOR_WAS_TRIGGERED_TO_OPEN';
    const DOOR_OPENING = 'DOOR_OPENING';
    const DOOR_OPENED = 'DOOR_OPENED';
    const DOOR_FINISHED = 'DOOR_FINISHED';
    const DOOR_BEHAVES_UNEXPECTEDLY = 'DOOR_BEHAVES_UNEXPECTEDLY';

    const PHOTO_INTERRUPTER_IS_ON = 'PHOTO_INTERRUPTER_IS_ON';
    const PHOTO_INTERRUPTER_IS_OFF = 'PHOTO_INTERRUPTER_IS_OFF';

    const NAME = '';

    /**
     * @var InputPin
     */
    protected $sensorPhotoInterrupter;

    /**
     * @var DoorInterface
     */
    protected $door;

    /**
     * @var string
     */
    protected $doorPhase;

    /**
     * @var string
     */
    protected $photoInterrupterPhase = self::PHOTO_INTERRUPTER_IS_OFF;

    /**
     * @var Microtime
     */
    protected $startTime = null;

    /**
     * @var Microtime
     */
    protected $doorOpenedTime = null;

    /**
     * @var int
     */
    protected $photoInterrupterCount = 0;

    public function __construct(DoorInterface $door, InputPin $sensorPhotoInterrupter)
    {
        $this->sensorPhotoInterrupter = $sensorPhotoInterrupter;
        $this->door                   = $door;
        $this->init();
    }

    protected function init()
    {
        $this->startTime = TimeProvider::microtime();

        if ($this->door->getState()->equals(DoorState::CLOSED())) {
            $this->doorPhase = self::DOOR_JUST_STARTED;
        }
        if ($this->door->getState()->equals(DoorState::UNKNOWN())) {
            $this->doorPhase = self::DOOR_OPENING;
        }
        if ($this->door->getState()->equals(DoorState::OPENED())) {
            $this->doorPhase      = self::DOOR_OPENED;
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
            $this->doorPhase = self::DOOR_WAS_TRIGGERED_TO_OPEN;
            $this->door->triggerControl();
        }
        if (self::DOOR_WAS_TRIGGERED_TO_OPEN === $this->doorPhase) {
            if ($this->door->getState()->equals(DoorState::UNKNOWN())) {
                $this->doorPhase = self::DOOR_OPENING;
            }
        }
        if (self::DOOR_OPENING === $this->doorPhase) {
            if ($this->door->getState()->equals(DoorState::OPENED())) {
                $this->doorPhase = self::DOOR_OPENED;
            }
            if ($this->door->getState()->equals(DoorState::CLOSED())) {
                $this->doorPhase = self::DOOR_BEHAVES_UNEXPECTEDLY;

                return State::FINISHED();
            }
        }
        if (self::DOOR_OPENED === $this->doorPhase) {
            if (null === $this->doorOpenedTime) {
                $this->doorOpenedTime = TimeProvider::microtime();
            }
            if ($this->door->getState()->equals(DoorState::UNKNOWN())) {
                $this->doorPhase = self::DOOR_BEHAVES_UNEXPECTEDLY;

                return State::FINISHED();
            }
            if ($this->isTargetReached() && $this->isDoorOpenedLongEnough()) {
                $this->doorPhase = self::DOOR_FINISHED;
                $this->door->triggerControl();

                return State::FINISHED();
            }
        }

        return State::RUNNING();
    }

    protected function updatePhotoInterrupter()
    {
        if (!$this->mustUpdatePhotoInterrupter()) {
            return;
        }

        if (self::PHOTO_INTERRUPTER_IS_OFF === $this->photoInterrupterPhase) {
            if ($this->sensorPhotoInterrupter->isOn()) {
                $this->photoInterrupterPhase = self::PHOTO_INTERRUPTER_IS_ON;
            }
        }
        if (self::PHOTO_INTERRUPTER_IS_ON === $this->photoInterrupterPhase) {
            if (!$this->sensorPhotoInterrupter->isOn()) {
                $this->photoInterrupterPhase = self::PHOTO_INTERRUPTER_IS_OFF;
                $this->photoInterrupterCount++;
            }
        }
    }

    /**
     * @return bool
     */
    protected function mustUpdatePhotoInterrupter()
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
    protected function isSequenceFinished()
    {
        return self::DOOR_FINISHED === $this->doorPhase || self::DOOR_BEHAVES_UNEXPECTEDLY === $this->doorPhase;
    }

    /**
     * @return bool
     */
    protected function isTargetReached()
    {
        return $this->photoInterrupterCount >= $this->getExpectedPhotoInterrupterCount();
    }

    /**
     * @return bool
     */
    protected function isTooEarlyForPhotoInterrupter()
    {
        if (self::DOOR_OPENED === $this->doorPhase) {
            return false;
        }

        $threshold = new Microtime(7);
        $now       = TimeProvider::microtime();

        $diff = $now->subtract($this->startTime);

        return $diff->isLessThan($threshold);
    }

    /**
     * @return bool
     */
    protected function isDoorOpenedLongEnough()
    {
        if (null === $this->doorOpenedTime) {
            return false;
        }
        $threshold = new Microtime(1);
        $now       = TimeProvider::microtime();

        $diff = $now->subtract($this->doorOpenedTime);

        return $diff->isGreaterThan($threshold);
    }

    /**
     * @return AutoSequenceName
     */
    public function getName()
    {
        return new AutoSequenceName(static::NAME);
    }

    /**
     * @return int
     */
    abstract protected function getExpectedPhotoInterrupterCount();
}