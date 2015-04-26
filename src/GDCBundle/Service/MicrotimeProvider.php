<?php

namespace GDCBundle\Service;

use GDCBundle\Model\Microtime;

class MicrotimeProvider
{
    /**
     * @var null|Microtime
     */
    private static $testNow = null;

    /**
     * @param Microtime $now
     */
    public static function setTestNow(Microtime $now)
    {
        self::$testNow = $now;
    }

    /**
     * @return Microtime
     */
    public static function now()
    {
        if (null !== self::$testNow) {
            return self::$testNow;
        }
        return new Microtime(microtime());
    }
}