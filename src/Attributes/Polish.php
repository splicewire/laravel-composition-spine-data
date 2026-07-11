<?php

namespace Rushing\CompositionSpineData\Attributes;

use Attribute;
use Rushing\CompositionSpineData\GenerationAttributesStrategy;

/**
 * Fences a Beat (property or class) out of auto-polish (issue 20): with `auto: false` the whole-composition
 * polish orchestrator skips this beat's cells even when they are otherwise eligible — e.g. a CTA or a
 * sensitive section a profile does not want machine-rewritten. Projected to the `x-polish` JSON-Schema
 * keyword by {@see GenerationAttributesStrategy}; the orchestrator reads the
 * keyword (via the walked Beat), never this PHP attribute. Absent the attribute a beat stays polish-eligible.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class Polish
{
    public function __construct(
        public bool $auto = true,
    ) {}
}
