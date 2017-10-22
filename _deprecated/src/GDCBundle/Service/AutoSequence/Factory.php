<?php

namespace GDCBundle\Service\AutoSequence;

use GDC\CommandQueue\Command;
use GDC\Door\DoorInterface;
use Pkj\Raspberry\PiFace\InputPin;

class Factory
{
    /**
     * @var DoorInterface
     */
    private $door;

    /**
     * @var InputPin
     */
    private $photoInterrupter;

    public function __construct(DoorInterface $door, InputPin $photoInterrupter)
    {
        $this->door             = $door;
        $this->photoInterrupter = $photoInterrupter;
    }

    /**
     * @param Command $command
     * @return TriggerDoor|null
     */
    public function createSequenceFor(Command $command)
    {
        if ($command->equals(Command::TRIGGER_DOOR())) {
            return new TriggerDoor($this->door);
        }
        if ($command->equals(Command::CLOSE_AFTER_ONE_TRANSIT())) {
            return new CloseAfterOneTransit($this->door, $this->photoInterrupter);
        }
        if ($command->equals(Command::CLOSE_AFTER_TWO_TRANSITS())) {
            return new CloseAfterTwoTransits($this->door, $this->photoInterrupter);
        }

        return null;
    }
}