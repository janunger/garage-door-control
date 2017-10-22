<?php

namespace GDCBundle\Event;

use GDC\CommandQueue\Command;
use Symfony\Component\EventDispatcher\Event;

class CommandIssuedEvent extends Event
{
    /**
     * @var Command
     */
    private $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * @return Command
     */
    public function getCommand()
    {
        return $this->command;
    }
}
