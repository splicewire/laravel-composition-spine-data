<?php

namespace Rushing\CompositionSpineData\Attributes;

use Attribute;
use Rushing\CompositionSpineData\GenerationAttributesStrategy;

/**
 * Declares a node's grounding SOURCES as grammar state (ADR-0076): an ordered typed source list
 * (`context_scope` id-or-inline, `webhook`, `facts`) whose resolved facts feed this node's
 * subtree pool. Class level declares a node's sources (the grammar ROOT feeds the whole
 * composition); property level supplements a beat's subtree. Request-supplied sources JOIN after
 * the declared set by default; `replace: true` on the request substitutes it.
 *
 * Projected to the `x-swc-grounding` keyword by {@see GenerationAttributesStrategy} — the bare
 * source list, or `{fusion, sources}` when a non-default fusion strategy is named (a
 * `composition.fuse.<handle>` capability). The generator reads the keyword, never this attribute.
 *
 * Seam note: the source TYPE names (`context_scope`, `webhook`, `facts`) are the ADR-0076 WIRE
 * vocabulary shared across contexts; the descriptor CONTENTS (selector fields, endpoints) are
 * host vocabulary — opaque here, parsed only by the host's resolvers.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class Grounding
{
    /**
     * @param  list<array<string, mixed>>  $sources  ordered typed source list — later sources refine earlier ones
     * @param  string|null  $fusion  optional named fusion strategy HANDLE (`composition.fuse.<handle>`)
     */
    public function __construct(
        public array $sources = [],
        public ?string $fusion = null,
    ) {}

    /**
     * The projected keyword value: the bare source list, or the `{fusion, sources}` object form
     * when a strategy is named.
     *
     * @return array<int|string, mixed>
     */
    public function keyword(): array
    {
        return $this->fusion === null
            ? $this->sources
            : ['fusion' => $this->fusion, 'sources' => $this->sources];
    }
}
