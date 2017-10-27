<?php

declare(strict_types=1);

namespace JUIT\GDC\Door;

interface DoorInterface
{
    public function getState(): State;

    public function triggerControl();
}
