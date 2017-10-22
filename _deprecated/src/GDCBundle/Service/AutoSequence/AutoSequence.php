<?php

namespace GDCBundle\Service\AutoSequence;

use GDCBundle\Model\AutoSequenceName;

interface AutoSequence
{
    /**
     * @return State
     */
    public function tick();

    /**
     * @return AutoSequenceName
     */
    public function getName();
}