<?php

declare(strict_types=1);

namespace JUIT\GDC\ControlSequence\Sequence;

use MyCLabs\Enum\Enum;

/**
 * @method static State FINISHED()
 * @method static State RUNNING()
 */
class State extends Enum
{
    const FINISHED = 'finished';
    const RUNNING = 'running';
}
