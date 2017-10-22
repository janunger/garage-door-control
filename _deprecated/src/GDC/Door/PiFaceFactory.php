<?php

namespace GDC\Door;

use Pkj\Raspberry\PiFace\Emulator\PiFace as Emulator;
use Pkj\Raspberry\PiFace\Hardware\PiFace as Hardware;
use Pkj\Raspberry\PiFace\Emulator\StateProvider;
use Pkj\Raspberry\PiFace;

class PiFaceFactory
{
    /**
     * @var string
     */
    private $emulatorDataDir;

    /**
     * @var bool
     */
    private $usePiFaceHardware;

    public function __construct($emulatorDataDir, $usePiFaceHardware)
    {
        $this->emulatorDataDir = $emulatorDataDir;
        $this->usePiFaceHardware = $usePiFaceHardware;
    }

    /**
     * @return PiFace
     */
    public function createInstance()
    {
        if ($this->usePiFaceHardware) {
            return $this->createHardwareInstance();
        }

        return $this->createEmulator();
    }

    /**
     * @return PiFace
     */
    private function createHardwareInstance()
    {
        $instance = Hardware::createInstance();
        $instance->init();

        return $instance;
    }

    /**
     * @return PiFace
     */
    private function createEmulator()
    {
        $dataDir = new \SplFileInfo($this->emulatorDataDir);
        $stateProvider = new StateProvider($dataDir);

        return new Emulator($stateProvider);
    }
}
