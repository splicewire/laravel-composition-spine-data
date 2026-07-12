<?php

namespace Splicewire\CompositionSpineData\Attributes;

use Attribute;

/**
 * Declares a Beat as a medium-neutral REPRISE of a named sibling Beat rather than a fresh generation
 * (composition-dialect-gaps issue 11). A chorus/refrain in a song, a recurring tagline/CTA in an
 * article, a motif in a video script — the same cross-beat repetition disposition in any medium.
 *
 * Projected to the `x-swc-repeat` keyword by {@see GenerationAttributesStrategy}; stripped by
 * `forLlmStrict` so it never reaches the model. Additive-safe: an engine that does not interpret it
 * generates the beat fresh (today's behavior).
 *
 * Two forms:
 *  - `#[Repeat(of: 'chorus')]` → verbatim reprise of the `chorus` sibling (skip-and-copy).
 *  - `#[Repeat(of: 'chorus', vary: 'change the final line')]` → controlled variation: the engine revises
 *    the source's content to satisfy `vary` while preserving its structure and every grounding token.
 *
 * The engine reprise is polish-fenced automatically (a polish pass must never desync a copy from its
 * source) and degrades gracefully: a repeat whose source has not drained yet generates fresh, never
 * errors.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class Repeat
{
    public function __construct(
        public string $of,
        public ?string $vary = null,
    ) {}

    /**
     * The `x-swc-repeat` value: a bare string for a verbatim reprise, or a `{of, vary}` object when a
     * variation instruction is present.
     *
     * @return string|array<string, string>
     */
    public function keyword(): string|array
    {
        return $this->vary === null
            ? $this->of
            : ['of' => $this->of, 'vary' => $this->vary];
    }
}
