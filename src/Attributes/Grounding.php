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
 * source list, or the object form when a non-default fusion strategy is named (a
 * `composition.fuse.<handle>` capability) and/or a beat PINS the groups it may draw from. The
 * generator reads the keyword, never this attribute.
 *
 * `only` is the SUBTRACTIVE counterpart of a property-level supplement: at BEAT level it hard-isolates
 * the beat (and its subtree) to the named grounding GROUPS — the drain narrows the subtree pool to
 * those groups before expansion, so a citation outside them is impossible by construction. Deterministic
 * platform behavior, never a prompt instruction. The group names are engine vocabulary (pool keys), not
 * host descriptors.
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
     * @param  list<string>  $only  optional PIN filter — the grounding GROUP names this beat's subtree may draw
     *                              from EXCLUSIVELY (hard isolation); empty means no narrowing (the whole pool)
     */
    public function __construct(
        public array $sources = [],
        public ?string $fusion = null,
        public array $only = [],
    ) {}

    /**
     * The projected keyword value: the bare source list when neither a strategy is named nor a pin filter
     * declared; otherwise the object form carrying `sources` plus `fusion` and/or `only` when present.
     * Ordered `fusion`, `only`, `sources` for a stable, diff-friendly projection.
     *
     * @return array<int|string, mixed>
     */
    public function keyword(): array
    {
        if ($this->fusion === null && $this->only === []) {
            return $this->sources;
        }

        $keyword = [];
        if ($this->fusion !== null) {
            $keyword['fusion'] = $this->fusion;
        }
        if ($this->only !== []) {
            $keyword['only'] = $this->only;
        }
        $keyword['sources'] = $this->sources;

        return $keyword;
    }
}
