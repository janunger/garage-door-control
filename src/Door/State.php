<?php

declare(strict_types=1);

namespace JUIT\GDC\Door;

use MyCLabs\Enum\Enum;

/**
 * @method static State CLOSED()
 * @method static State OPENED()
 * @method static State UNKNOWN()
 * @method static State HARDWARE_ERROR()
 */
class State extends Enum
{
    const CLOSED = 'closed';
    const OPENED = 'opened';
    const UNKNOWN = 'unknown';
    const HARDWARE_ERROR = 'hardware_error';
}
