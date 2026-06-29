<?php

declare(strict_types=1);

namespace Rushing\CompositionSpineData\Attributes;

use Attribute;

/**
 * Marks a composition-schema node (or property) as a Beat the interpreter walks.
 * Projected to the `x-beat` JSON-Schema keyword (the keyword value is the
 * BeatKind) by GenerationAttributesStrategy; stripped from the LLM-facing schema
 * by forLlmStrict. The interpreter reads `x-beat` — never this PHP attribute.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
final class Beat
{
    public function __construct(
        public readonly BeatKind $kind = BeatKind::Expandable,
    ) {}
}
