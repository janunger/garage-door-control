<?php

namespace GDC\Sensor;

use MyCLabs\Enum\Enum;

/**
 * @method static Role DOOR_CLOSED
 * @method static Role DOOR_OPENED
 * @method static Role PHOTO_INTERRUPTER
 */
class Role extends Enum
{
    const DOOR_CLOSED = 'door_closed';
    const DOOR_OPENED = 'door_opened';
    const PHOTO_INTERRUPTER = 'photo_interrupter';
}