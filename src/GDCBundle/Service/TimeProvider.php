<?php

namespace GDCBundle\Service;

class TimeProvider
{
    /**
     * @var float|null
     */
    private static $testMicrotime = null;

    /**
     * @return string
     */
    public static function microtime()
    {
        if (null !== self::$testMicrotime) {
            return self::$testMicrotime;
        }

        return microtime(true);
    }

    /**
     * @param float|null $testMicrotime
     */
    public static function setTestMicrotime($testMicrotime = null)
    {
        self::$testMicrotime = $testMicrotime;
    }
}