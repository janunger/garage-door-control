<?php

namespace GDCBundle\Service\AutoSequence;

use MyCLabs\Enum\Enum;

/**
 * @method static State FINISHED()
 * @method static State RUNNING()
 */
class State extends Enum
{
    const FINISHED = 'finished';
    const RUNNING = 'running';

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