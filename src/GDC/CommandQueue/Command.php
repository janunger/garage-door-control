<?php

namespace GDC\CommandQueue;

use MyCLabs\Enum\Enum;

/**
 * @method static TRIGGER_DOOR
 */
class Command extends Enum
{
    const TRIGGER_DOOR = 'trigger-door';

    /**
     * @param Command $other
     * @return bool
     */
    public function equals(Command $other)
    {
        return $this->getValue() === $other->getValue();
    }
}
