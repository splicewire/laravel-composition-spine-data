<?php

namespace Splicewire\CompositionSpineData\Wire;

/**
 * Shared spine wire DTO (ADR-0093 tier 5).
 *
 * The analyzed grounding pool the coherence read returns: the fused `snapshot`, grouped by the
 * caller's configured dimension. Each group is the list of contributing sources' retained entries,
 * every entry stamped with its dimension's `coherence_state` (`agreement`/`conflict`/`gap`) —
 * nothing blended, so a consumer can quote and attribute each source and honestly say the sources
 * disagree where they do.
 */
class CoherenceAnalysis
{
    /**
     * @param  array<string, array<int, array<string, mixed>>>  $snapshot  dimension => retained entries (each carrying coherence_state)
     */
    public function __construct(
        public array $snapshot = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data  the unwrapped `data` record (carries `snapshot`), or the bare snapshot.
     */
    public static function fromArray(array $data): self
    {
        return new static(
            snapshot: (array) ($data['snapshot'] ?? $data),
        );
    }

    /**
     * The retained entries for one dimension, or an empty list when the pool named none.
     *
     * @return array<int, array<string, mixed>>
     */
    public function dimension(string $dimension): array
    {
        return $this->snapshot[$dimension] ?? [];
    }

    /**
     * The cross-source state for one dimension (read off any retained entry), or null when absent.
     */
    public function stateFor(string $dimension): ?string
    {
        $state = $this->snapshot[$dimension][0]['coherence_state'] ?? null;

        return is_string($state) ? $state : null;
    }
}
