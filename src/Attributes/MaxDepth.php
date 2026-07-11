<?php

namespace Rushing\CompositionSpineData\Attributes;

use Attribute;
use Rushing\CompositionSpineData\GenerationAttributesStrategy;

/**
 * Caps the recursion depth of grammar expansion at a Beat (property or class) subtree
 * root: the interpreter clamps the effective depth to the engine cap and demotes an
 * expandable beat at the ceiling. Projected to the `x-max-depth` JSON-Schema keyword by
 * {@see GenerationAttributesStrategy}; stripped from the
 * LLM-facing schema by `forLlmStrict` like every other `x-*`. The interpreter reads
 * `x-max-depth` — never this PHP attribute.
 *
 * Before this attribute existed, `x-max-depth` was a read-side-only keyword (the engine
 * honoured it but no attribute emitted it, so a leaf could not declare it); this is the
 * emitter that makes it authorable like its `#[Beat]`/`#[Pause]`/`#[Cache]` siblings.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class MaxDepth
{
    public function __construct(
        public int $depth,
    ) {}

    public function keyword(): int
    {
        return $this->depth;
    }
}
