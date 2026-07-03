<?php

declare(strict_types=1);

namespace Rushing\CompositionSpineData\Attributes;

use Attribute;

/**
 * Fences a Beat (property or class) out of auto-polish (issue 20): with `auto: false` the whole-composition
 * polish orchestrator skips this beat's cells even when they are otherwise eligible — e.g. a CTA or a
 * sensitive section a profile does not want machine-rewritten. Projected to the `x-polish` JSON-Schema
 * keyword by {@see \Rushing\CompositionSpineData\GenerationAttributesStrategy}; the orchestrator reads the
 * keyword (via the walked Beat), never this PHP attribute. Absent the attribute a beat stays polish-eligible.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
final class Polish
{
    public function __construct(
        public readonly bool $auto = true,
    ) {}
}
