<?php

namespace Rushing\CompositionSpineData\Attributes;

use Attribute;
use Rushing\CompositionSpineData\Schema\BeatGrammar;

/**
 * Marks a property the engine fills from the Composition's grounding snapshot
 * rather than from the model. The optional source name selects a registered
 * ground capability / grounding type; omitted, the property name is the source.
 * Projected to the `x-ground` JSON-Schema keyword (value: source name, or `true`
 * for the property-name default) by GenerationAttributesStrategy and stripped by
 * forLlmStrict.
 *
 * Also valid at class level, where it marks every beat node a {@see BeatGrammar}
 * projects from the leaf as grounded (`x-ground: true`).
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class Ground
{
    public function __construct(
        public ?string $from = null,
    ) {}

    public function keyword(): string|bool
    {
        return $this->from ?? true;
    }
}
