<?php

namespace Splicewire\CompositionSpineData\Contracts;

use Splicewire\CompositionSpineData\Angles\ComposedShape;

/**
 * An editorial angle's identity. The app supplies the concrete vocabulary (a backed enum whose cases
 * are the angle keys); the engine only needs the stable string identity to register contributors and
 * to compare angles inside a {@see ComposedShape}.
 */
interface AngleContract
{
    /**
     * The stable string identity of this angle (e.g. "travel", "watch").
     */
    public function value(): string;
}
