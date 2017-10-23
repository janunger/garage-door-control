<?php

declare(strict_types=1);

namespace JUIT\GDC\WatchDog;

use JUIT\GDC\Door\State;

class DoorStateWriter
{
    /**
     * @var \SplFileInfo
     */
    private $filePath;

    public function __construct(\SplFileInfo $filePath)
    {
        $this->filePath = $filePath;
    }

    public function write(State $state, \DateTimeImmutable $date)
    {
        $data = [
            'doorState' => $state->getValue(),
            'date' => $date->format(DATE_ATOM),
        ];
        file_put_contents($this->filePath->getPathname(), json_encode($data));
    }
}
