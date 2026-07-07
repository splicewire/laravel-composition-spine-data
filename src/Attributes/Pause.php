<?php

namespace Rushing\CompositionSpineData\Attributes;

use Attribute;

/**
 * Marks a Beat (property or class) as a pause checkpoint: the interpreter emits
 * the cell it yields in `NeedsReview` and does NOT expand that beat's children
 * until a human approves it. A paused plan Beat halts section writing — the HITL
 * seam. Projected to the `x-pause` JSON-Schema keyword by
 * {@see GenerationAttributesStrategy}; stripped from the LLM-facing
 * schema by `forLlmStrict` like every other `x-*`. The interpreter reads
 * `x-pause` — never this PHP attribute.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class Pause
{
    public function __construct(
        public bool $enabled = true,
    ) {}
}
