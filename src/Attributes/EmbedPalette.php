<?php

namespace Splicewire\CompositionSpineData\Attributes;

use Attribute;
use Splicewire\CompositionSpineData\GenerationAttributesStrategy;
use Spatie\LaravelData\Data;

/**
 * Marks an array property whose items the model MAY place from a fixed palette of embed
 * node {@see Data} schemas — a model-filled field, NOT a separate
 * expandable beat. The model writes the embed inline during the host node's generation
 * (e.g. a section), so it rides that node's `fields` through the engine's recursive
 * persist/export with no engine change.
 *
 * Each listed embed class is a `Data` whose projected item schema is hardened like the rest
 * of the graph (additionalProperties:false + required) and carries a fixed `type`
 * discriminator field (a JSON-Schema `const`) so a persisted `embeds[]` entry is
 * self-identifying — the host reads `type` to know which embed to rehydrate. The
 * discriminator value comes from the embed class's `EMBED_TYPE` constant.
 *
 * Projection ({@see GenerationAttributesStrategy}):
 *  - ONE type  → `{type: array, items: <projected strict item schema of that type>}`.
 *  - N>1 types → `{type: array, items: {anyOf: [<each type's item schema>]}}`.
 *
 * The single-type case deliberately avoids a JSON-Schema `oneOf`/`anyOf` inside an array's
 * `items`, which OpenAI strict structured output rejects. Strict-output compatibility for the
 * MULTI-type (`anyOf`) case is UNVERIFIED here — a later issue's concern; the shape is
 * designed for it but not exercised by a strict provider yet.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class EmbedPalette
{
    /**
     * @param  list<class-string>  $types  embed-node `Data` class-strings the model may place.
     */
    public function __construct(
        public array $types,
    ) {}
}
