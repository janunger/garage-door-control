<?php

namespace GDCBundle\Service;

use GDC\Door\State;
use GDCBundle\Model\Microtime;

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

    public function write(State $state, Microtime $time)
    {
        file_put_contents(
            $this->filePath,
            '{"doorState":"' . $state->getValue() . '","date":"' . date(DATE_ISO8601, $time->getIntegerPart()) . '"}'
        );
    }
}
