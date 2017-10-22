<?php

declare(strict_types=1);

namespace JUIT\GDC\PiFace;

use Symfony\Component\Process\Process;

class OutputPin
{
    /**
     * @var int
     */
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function trigger(int $durationMilliseconds)
    {
        $command = sprintf('python3 %s/bin/trigger.py %s %s', __DIR__, $this->id, $durationMilliseconds);
        $process = new Process($command);
        $process->mustRun();
    }
}
