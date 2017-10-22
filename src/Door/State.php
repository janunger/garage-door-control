<?php

declare(strict_types=1);

namespace JUIT\GDC\Door;

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
}
