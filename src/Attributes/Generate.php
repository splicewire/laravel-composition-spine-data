<?php

namespace Splicewire\CompositionSpineData\Attributes;

use Attribute;
use Splicewire\CompositionSpineData\Schema\BeatGrammar;

/**
 * Marks a property the model generates when its Beat is expanded. The optional
 * handler name selects a registered generate capability; omitted, the profile's
 * default generate capability is used. Projected to the `x-generate` JSON-Schema
 * keyword (value: handler name, or `true` for the default) by
 * GenerationAttributesStrategy and stripped by forLlmStrict.
 *
 * Also valid at class level, where it marks every beat node a {@see BeatGrammar}
 * projects from the leaf as generate-able (`x-generate: true`).
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class Generate
{
    public function __construct(
        public ?string $with = null,
    ) {}

    public function keyword(): string|bool
    {
        return $this->with ?? true;
    }
}
