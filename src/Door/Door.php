<?php

declare(strict_types=1);

namespace JUIT\GDC\Door;

use JUIT\GDC\Model\InputPinIdDoorClosed;
use JUIT\GDC\Model\InputPinIdDoorOpened;
use JUIT\PiFace\OutputPin;
use JUIT\PiFace\PiFace;

class Door implements DoorInterface
{
    /**
     * @var PiFace
     */
    private $piFace;

    /**
     * @var InputPinIdDoorClosed
     */
    private $inputPinIdDoorClosed;

    /**
     * @var InputPinIdDoorOpened
     */
    private $inputPinIdDoorOpened;

    /**
     * @var OutputPin
     */
    private $actorMotor;

    public function __construct(
        PiFace $piFace,
        InputPinIdDoorClosed $inputPinIdDoorClosed,
        InputPinIdDoorOpened $inputPinIdDoorOpened,
        OutputPin $actorMotor
    ) {
        $this->piFace               = $piFace;
        $this->actorMotor           = $actorMotor;
        $this->inputPinIdDoorClosed = $inputPinIdDoorClosed;
        $this->inputPinIdDoorOpened = $inputPinIdDoorOpened;
    }

    public function getState(): State
    {
        $doorState = $this->piFace->readInputPins();
        $isClosed  = $doorState->isPinOn($this->inputPinIdDoorClosed->getValue());
        $isOpened  = $doorState->isPinOn($this->inputPinIdDoorOpened->getValue());

        if ($isClosed && $isOpened) {
            return State::HARDWARE_ERROR();
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
