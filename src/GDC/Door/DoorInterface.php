<?php

namespace GDC\Door;

interface DoorInterface
{
    /**
     * @return State
     * @throws HardwareErrorException
     */
    public function getState();

    /**
     * @return void
     */
    public function triggerControl();
}
