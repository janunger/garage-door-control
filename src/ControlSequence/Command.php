<?php

declare(strict_types=1);

namespace JUIT\GDC\ControlSequence;

use MyCLabs\Enum\Enum;

/**
 * @method static Command TRIGGER_DOOR()
 * @method static Command CLOSE_AFTER_ONE_TRANSIT()
 * @method static Command CLOSE_AFTER_TWO_TRANSITS()
 * @method static Command CANCEL()
 */
class Command extends Enum
{
    const TRIGGER_DOOR = 'trigger-door';
    const CLOSE_AFTER_ONE_TRANSIT = 'close-after-one-transit';
    const CLOSE_AFTER_TWO_TRANSITS = 'close-after-two-transits';
    const CANCEL = 'cancel';
}
