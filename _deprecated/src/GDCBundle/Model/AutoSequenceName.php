<?php

namespace GDCBundle\Model;

class AutoSequenceName
{
    /**
     * @var string
     */
    private $value;

    public function __construct($value = '')
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}