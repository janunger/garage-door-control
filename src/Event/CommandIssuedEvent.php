<?php

declare(strict_types=1);

namespace JUIT\GDC\Event;

use JUIT\GDC\ControlSequence\Command;
use Symfony\Component\EventDispatcher\Event;

class CommandIssuedEvent extends Event
{
    const NAME = 'gdc.command_issued';

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
