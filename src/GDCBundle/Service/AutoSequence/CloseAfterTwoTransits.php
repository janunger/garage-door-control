<?php

namespace GDCBundle\Service\AutoSequence;

class CloseAfterTwoTransits extends AbstractCloseAfterNTransits
{
    const NAME = 'close-after-two-transits';

    /**
     * @return State
     */
    public function tick()
    {
        return State::FINISHED();
    }
}