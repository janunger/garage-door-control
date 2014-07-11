<?php

namespace GDC\WatchDog;

use Carbon\Carbon;
use GDC\Door;

class WatchDog
{
    /**
     * @var Door
     */
    private $door;

    /**
     * @var string|null
     */
    private $state = null;

    /**
     * @var \DateTime|null
     */
    private $stateChangeDate = null;

    /**
     * @var Messenger
     */
    private $messenger;

    public function __construct(Door $door, Messenger $messenger)
    {
        $this->door = $door;
        $this->messenger = $messenger;
    }

    public function execute()
    {
        try {
            $state = $this->door->getState();
        } catch (Door\HardwareErrorException $e) {
            $state = 'hardware_error';
        }

        if ($state === $this->state) {
            return;
        }

        $this->stateChangeDate = Carbon::now();
        $this->state = $state;
        $this->messenger->send($this->state, $this->stateChangeDate);
    }
}
