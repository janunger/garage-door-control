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
    public function __construct($value = null)
    {
        if (null === $value) {
            $value = microtime(true);
        }
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
        if (preg_match('/^-?\d+\.\d{1,4}$/', $value)) {
            return (string)$value;
        }
        throw new \InvalidArgumentException("Unexpected microtime representation '$value'");
    }

    /**
     * @return string
     */
    public function getIntegerPart()
    {
        return substr($this->value, 0, strpos($this->value, '.'));
    }

    /**
     * @param Microtime $other
     * @return Microtime
     */
    public function subtract(Microtime $other)
    {
        return new Microtime(bcsub($this->value, $other->value, 4));
    }

    /**
     * @param Microtime $other
     * @return bool
     */
    public function isGreaterThan(Microtime $other)
    {
        return 1 === bccomp($this->value, $other->value, 4);
    }
}
