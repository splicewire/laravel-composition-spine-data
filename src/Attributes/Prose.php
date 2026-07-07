<?php

namespace Rushing\CompositionSpineData\Attributes;

use Attribute;

/**
 * The prose disposition of a grounding field: how its facts may be discussed in
 * body prose. Carried on a grounding `Data` class's field and projected to the
 * `x-prose` JSON-Schema keyword (with an optional `x-prose-note`) by
 * GenerationAttributesStrategy. The engine derives prose discipline from these
 * keywords — so "do not name the booking provider" lives in the grounding type's
 * schema, not in a blanket prompt rule.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Prose
{
    public function __construct(
        public ProseRole $role,
        public ?string $note = null,
    ) {}
}
