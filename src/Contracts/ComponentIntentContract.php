<?php

namespace Rushing\CompositionSpineData\Contracts;

/**
 * An abstract component an angle encourages the body to use (e.g. "destination CTA", "where-to-watch",
 * "product showcase") — independent of how any host renders it. The app supplies the concrete
 * vocabulary (a backed enum) and owns both the stable identity and the human-readable prompt label for
 * each case. A host-side shape→beat-guidance stitcher reads {@see self::label()} when it lists the
 * encouraged components; the concrete label strings never enter a shared package.
 */
interface ComponentIntentContract
{
    /**
     * The stable string identity of this component intent (e.g. "destination-cta", "where-to-watch").
     */
    public function value(): string;

    /**
     * The human-readable prompt label for this component intent (e.g. "an Expedia destination CTA card",
     * "a where-to-watch box"), owned by the host enum. Mirrors how
     * {@see ConversionIntentContract::primaryCtaGuidance()} lets a host own its CTA wording, so the
     * concrete labels stay reviewable in the host repo and out of every shared package.
     */
    public function label(): string;
}
