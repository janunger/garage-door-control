<?php

namespace GDCBundle\Event;

use GDCBundle\Model\AutoSequenceName;
use Symfony\Component\EventDispatcher\Event;

class AutoSequenceStartedEvent extends Event
{
    /**
     * @var AutoSequenceName
     */
    private $sequenceName;

    public function __construct(AutoSequenceName $sequenceName)
    {
        $this->sequenceName = $sequenceName;
    }

    /**
     * @return AutoSequenceName
     */
    public function getSequenceName()
    {
        return $this->sequenceName;
    }
}