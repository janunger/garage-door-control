<?php

namespace GDCBundle\Service\AutoSequence;

class CloseAfterOneTransit extends AbstractCloseAfterNTransits
{
    const NAME = 'close-after-one-transit';

    /**
     * @return int
     */
    protected function getExpectedPhotoInterrupterCount()
    {
        return 1;
    }
}