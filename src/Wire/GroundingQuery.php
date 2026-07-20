<?php

namespace Splicewire\CompositionSpineData\Wire;

/**
 * Shared spine wire DTO (ADR-0093 tier 5) — the vocabulary that crosses the `api/v1` wire a
 * satellite may call, defined once and consumed by every party (engine, connector, satellite).
 *
 * Inbound grounding query the platform POSTs during generation: `{type, ...}`. `type` selects
 * which grounding source the antenna answers from; everything else (e.g. `ref`) is preserved in
 * {@see $attributes} so an antenna reads arbitrary selectors without this DTO modelling every
 * vertical's shape.
 */
class GroundingQuery
{
    /**
     * @param  array<string, mixed>  $attributes  the full raw query (incl. `type`).
     */
    public function __construct(
        public string $type,
        public array $attributes = [],
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new static(
            type: (string) ($payload['type'] ?? 'default'),
            attributes: $payload,
        );
    }

    /**
     * Read an arbitrary selector from the query (e.g. ->get('ref')).
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }
}
