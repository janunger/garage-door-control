<?php

namespace GDCBundle\Event;

class WatchDogEvents
{
    const RESTARTED = 'watchdog.restarted';
    const DOOR_STATE_CHANGE = 'watchdog.door_state_change';
    const DOOR_OPENING = 'watchdog.door_opening';
    const DOOR_CLOSED = 'watchdog.door_closed';
    const HARDWARE_ERROR = 'watchdog.hardware_error';
}