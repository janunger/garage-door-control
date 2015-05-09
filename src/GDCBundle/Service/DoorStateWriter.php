<?php

namespace GDCBundle\Service;

use GDC\Door\State;
use GDCBundle\Event\AutoSequenceStartedEvent;
use GDCBundle\Model\AutoSequenceName;
use GDCBundle\Model\Microtime;

class DoorStateWriter
{
    /**
     * @var \SplFileInfo
     */
    private $filePath;

    /**
     * @var AutoSequenceName|null
     */
    private $currentAutoSequenceName = null;

    public function __construct(\SplFileInfo $filePath)
    {
        $this->filePath = $filePath;
    }

    public function onAutoSequenceStarted(AutoSequenceStartedEvent $event)
    {
        $this->currentAutoSequenceName = $event->getSequenceName();
    }

    public function write(State $state, Microtime $time)
    {
        $data = [
            'doorState'    => $state->getValue(),
            'date'         => date(DATE_ISO8601, $time->getIntegerPart()),
            'autoSequence' => $this->getAutoSequenceInfo()
        ];
        file_put_contents($this->filePath, json_encode($data));
    }

    /**
     * @return null|string
     */
    private function getAutoSequenceInfo()
    {
        if (null === $this->currentAutoSequenceName) {
            return null;
        }

        return $this->currentAutoSequenceName->getValue();
    }
}
