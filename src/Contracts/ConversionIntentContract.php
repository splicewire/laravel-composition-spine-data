<?php

namespace Rushing\CompositionSpineData\Contracts;

/**
 * The conversion direction an angle leads with. The app supplies the concrete vocabulary (a backed
 * enum). Beyond its string identity, an intent carries a profile-independent statement of the call to
 * action it leads with; a generative profile clarifies that base guidance into its own concrete
 * affordances via `realizeIntentCta()`.
 */
interface ConversionIntentContract
{
    /**
     * The stable string identity of this intent (e.g. "travel", "watch", "shop").
     */
    public function value(): string;

    /**
     * Profile-independent guidance for the primary call to action this intent leads with. The active
     * profile may pass this through unchanged or override it with profile-specific phrasing.
     */
    public function primaryCtaGuidance(): string;
}
