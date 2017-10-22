<?php

declare(strict_types=1);

namespace JUIT\GDC\Event;

class WatchDogEvents
{
    const RESTARTED = 'watchdog.restarted';
    const DOOR_OPENING = 'watchdog.door_opening';
    const DOOR_CLOSED = 'watchdog.door_closed';
    const HARDWARE_ERROR = 'watchdog.hardware_error';
}
