<?php

namespace GDCBundle\Service\AutoSequence;

class CloseAfterTwoTransits extends AbstractCloseAfterNTransits
{
    const NAME = 'close-after-two-transits';

    /**
     * @return int
     */
    protected function getExpectedPhotoInterrupterCount()
    {
        return 2;
    }
}