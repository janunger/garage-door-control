<?php

namespace GDCBundle\Service\SensorLogger;

use GDCBundle\Entity\SensorLogEntry;
use GDCBundle\Entity\SensorLogEntryRepository;
use GDCBundle\Model\Microtime;

class SensorLogger
{
    /**
     * @var
     */
    private $logEntryRepository;

    /**
     * @var StateWatcher[]
     */
    private $sensors = [];

    public function __construct(
        SensorLogEntryRepository $logEntryRepository,
        array $sensors
    ) {
        $this->logEntryRepository = $logEntryRepository;
        $this->sensors = $sensors;
    }

    public function execute()
    {
        foreach ($this->sensors as $sensor) {
            if ($sensor->isOn() !== $sensor->wasOn()) {
                $this->log($sensor);
            }
            $sensor->saveState();
        }
    }

    private function log(StateWatcher $sensor)
    {
        $this->logEntryRepository->save(new SensorLogEntry(
            $sensor->getRole(),
            $sensor->isOn(),
            new Microtime()
        ));
    }
}