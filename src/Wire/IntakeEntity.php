<?php

namespace Splicewire\Composition\Wire;

/**
 * Shared spine wire DTO (ADR-0093 tier 5).
 *
 * Typed view over the provision-entity envelope (`{data: {id, name, definition_ref, silo_id}}`)
 * returned by `POST /api/v1/intake/entities`. Only the fields a satellite needs to keep driving the
 * subject (submit sections, read completeness, reassess) are surfaced; the platform intake
 * vocabulary stays domain-agnostic, so no merchant/restaurant fields live here.
 */
class IntakeEntity
{
    public function __construct(
        public string $id,
        public string $name,
        public string $definitionRef,
        public ?string $siloId = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data  the unwrapped `data` record.
     */
    public static function fromArray(array $data): self
    {
        return new static(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            definitionRef: (string) ($data['definition_ref'] ?? ''),
            siloId: isset($data['silo_id']) ? (string) $data['silo_id'] : null,
        );
    }
}
