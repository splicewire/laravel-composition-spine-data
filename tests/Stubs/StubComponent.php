<?php

namespace Splicewire\CompositionSpineData\Tests\Stubs;

use Splicewire\CompositionSpineData\Contracts\ComponentIntentContract;

/**
 * A stand-in component intent that owns its own human label, exercising the host-owned
 * {@see ComponentIntentContract::label()} a shape→beat-guidance stitcher reads.
 */
enum StubComponent: string implements ComponentIntentContract
{
    case Widget = 'widget';

    public function value(): string
    {
        return $this->value;
    }

    public function label(): string
    {
        return 'a widget';
    }
}
