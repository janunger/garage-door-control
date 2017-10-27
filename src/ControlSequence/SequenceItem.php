<?php

declare(strict_types=1);

namespace JUIT\GDC\ControlSequence;

class SequenceItem
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Command
     */
    private $command;

    public function __construct(int $id, Command $command)
    {
        $this->id = $id;
        $this->command = $command;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCommand(): Command
    {
        return $this->command;
    }
}
