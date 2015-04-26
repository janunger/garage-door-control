<?php

namespace GDCBundle\Model;

class Microtime
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $this->normalize($value);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return string
     */
    private function normalize($value)
    {
        if (preg_match('/^0\.(?P<msec>\d+) (?P<sec>\d+)$/', $value, $matches)) {
            return $matches['sec'] . $matches['msec'];
        }
        if (preg_match('/^\d+$/', $value)) {
            return $value;
        }
        throw new \InvalidArgumentException("Unexpected microtime representation '$value'");
    }
}