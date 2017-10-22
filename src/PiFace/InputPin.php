<?php

declare(strict_types=1);

namespace JUIT\GDC\PiFace;

use Symfony\Component\Process\Process;

class InputPin
{
    /**
     * @var int
     */
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function isOn(): bool
    {
        $command = sprintf('python3 %s/bin/read.py %s', __DIR__, $this->id);
        $process = new Process($command);
        $process->mustRun();

        return trim($process->getOutput()) === '1';
    }
}
