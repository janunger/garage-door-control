<?php

declare(strict_types=1);

namespace JUIT\GDC\ControlSequence\Sequence;

interface Sequence
{
    public function tick(): State;
}
