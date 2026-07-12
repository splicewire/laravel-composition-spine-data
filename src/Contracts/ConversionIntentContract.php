<?php

namespace Splicewire\CompositionSpineData\Contracts;

/**
 * The conversion direction an angle leads with. The app supplies the concrete vocabulary (a backed
 * enum). Beyond its string identity, an intent carries a host-independent statement of the call to
 * action it leads with; the host-side shape→beat-guidance stitcher reads {@see self::primaryCtaGuidance()}
 * when it composes the primary call to action, so the concrete CTA wording stays owned by the host enum.
 */
interface ConversionIntentContract
{
    /**
     * The stable string identity of this intent (e.g. "travel", "watch", "shop").
     */
    public function value(): string;

    /**
     * Host-owned guidance for the primary call to action this intent leads with. The host-side stitcher
     * reads it verbatim; a host may pass it through unchanged or phrase it however its domain prefers.
     */
    public function primaryCtaGuidance(): string;
}
