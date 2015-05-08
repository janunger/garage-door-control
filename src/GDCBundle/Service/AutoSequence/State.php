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
}