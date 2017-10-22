<?php

namespace GDC\Door;

use MyCLabs\Enum\Enum;

/**
 * @method static State CLOSED()
 * @method static State OPENED()
 * @method static State UNKNOWN()
 */
class State extends Enum
{
    const CLOSED = 'closed';
    const OPENED = 'opened';
    const UNKNOWN = 'unknown';

    /**
     * @param State|null $other
     * @return bool
     */
    public function equals(State $other = null)
    {
        if (null === $other) {
            return false;
        }

        return $this->getValue() === $other->getValue();
    }
}
