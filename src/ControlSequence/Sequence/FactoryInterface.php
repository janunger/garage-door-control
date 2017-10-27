<?php

declare(strict_types=1);

namespace JUIT\GDC\ControlSequence\Sequence;

use JUIT\GDC\ControlSequence\Command;

interface FactoryInterface
{
    public function createSequenceFor(Command $command): Sequence;
}
