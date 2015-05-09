<?php

namespace GDCBundle\Service\AutoSequence;

use GDCBundle\Model\AutoSequenceName;

class CloseAfterTwoTransits implements AutoSequence
{
    const NAME = 'close-after-two-transits';

    /**
     * @return State
     */
    public function tick()
    {
        return State::FINISHED();
    }

    /**
     * @return AutoSequenceName
     */
    public function getName()
    {
        return new AutoSequenceName(self::NAME);
    }
}