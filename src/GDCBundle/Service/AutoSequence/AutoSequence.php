<?php

namespace GDCBundle\Service\AutoSequence;

interface AutoSequence
{
    /**
     * @return State
     */
    public function tick();
}