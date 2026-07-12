<?php

namespace Splicewire\CompositionSpineData\Tests\Stubs;

use Splicewire\CompositionSpineData\Angles\AngleComposer;
use Splicewire\CompositionSpineData\Contracts\AngleContract;

/**
 * A vocabulary-free stand-in for a host's angle enum: one real angle and one neutral base, enough to
 * drive {@see AngleComposer} without any host package.
 */
enum StubAngle: string implements AngleContract
{
    case Feature = 'feature';
    case Neutral = 'neutral';

    public function value(): string
    {
        return $this->value;
    }
}
