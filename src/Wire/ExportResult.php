<?php

namespace Splicewire\Composition\Wire;

/**
 * Shared spine wire DTO (ADR-0093 tier 5).
 *
 * The Compositions export (`GET /api/v1/compositions/{id}/export?format=...`) returns a RAW
 * deliverable body, not a JSON envelope. This pairs that body with its content type so consumers
 * don't reach into the underlying response. (The Saloon `Response`→DTO adapter lives connector-side;
 * the spine vocabulary stays transport-agnostic.)
 */
class ExportResult
{
    public function __construct(
        public string $contentType,
        public string $body,
    ) {}

    /**
     * @param  array{contentType?: string, body?: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new static(
            contentType: (string) ($data['contentType'] ?? 'text/plain'),
            body: (string) ($data['body'] ?? ''),
        );
    }
}
