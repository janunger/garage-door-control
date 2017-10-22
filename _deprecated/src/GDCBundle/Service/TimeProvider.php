<?php

namespace GDCBundle\Service;

use GDCBundle\Model\Microtime;

class TimeProvider
{
    /**
     * @var Microtime|null
     */
    private static $testMicrotime = null;

    /**
     * @return Microtime
     */
    public static function microtime()
    {
        if (null !== self::$testMicrotime) {
            return self::$testMicrotime;
        }

        return new Microtime(microtime(true));
    }

    /**
     * @param Microtime|null $testMicrotime
     */
    public static function setTestMicrotime(Microtime $testMicrotime = null)
    {
        self::$testMicrotime = $testMicrotime;
    }
}