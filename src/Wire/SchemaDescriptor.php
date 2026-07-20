<?php

namespace Splicewire\CompositionSpineData\Wire;

/**
 * Shared spine wire DTO (ADR-0093 tier 5).
 *
 * Typed view over a registry schema lookup (`GET /api/v1/schema-registry/...`): the resolved
 * absolute `$id`, its integer `version`, and the frozen JSON Schema `document`. Lets a satellite
 * discover a spine's current version over HTTP without already knowing an exact `$id`.
 *
 * `latest` returns `{$id, version, schema}`; an exact `show` returns the bare schema document —
 * {@see self::fromArray()} normalizes both.
 */
class SchemaDescriptor
{
    /**
     * @param  array<string, mixed>  $document  the frozen JSON Schema artifact.
     */
    public function __construct(
        public string $id,
        public ?int $version,
        public array $document,
    ) {}

    /**
     * @param  array<string, mixed>  $data  the unwrapped `data` record.
     */
    public static function fromArray(array $data): self
    {
        // `latest` wraps as {$id, version, schema}; `show` returns the bare schema doc.
        $document = is_array($data['schema'] ?? null) ? $data['schema'] : $data;
        $id = $data['$id'] ?? ($document['$id'] ?? '');
        $version = $data['version'] ?? null;

        return new static(
            id: (string) $id,
            version: $version === null ? null : (int) $version,
            document: is_array($document) ? $document : [],
        );
    }
}
