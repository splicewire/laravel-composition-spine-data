<?php

namespace Splicewire\Composition\Wire;

/**
 * Shared spine wire DTO (ADR-0093 tier 5).
 *
 * Inbound publish notification — the platform tells the antenna a composition was published (or
 * flagged NeedsReview) and hands it the compiled deliverable. Mirrors the export payload shape: an
 * id + status + the raw deliverable body and its content type. Unmodelled fields are preserved in
 * {@see $attributes}.
 */
class PublishNotification
{
    /**
     * @param  array<string, mixed>  $attributes  the full raw payload.
     */
    public function __construct(
        public string $compositionId,
        public ?string $status = null,
        public ?string $contentType = null,
        public ?string $body = null,
        public array $attributes = [],
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new static(
            compositionId: (string) ($payload['composition_id'] ?? $payload['id'] ?? ''),
            status: isset($payload['status']) ? (string) $payload['status'] : null,
            contentType: isset($payload['content_type']) ? (string) $payload['content_type'] : null,
            body: isset($payload['body']) ? (string) $payload['body'] : null,
            attributes: $payload,
        );
    }

    public function needsReview(): bool
    {
        return $this->status === 'NeedsReview' || $this->status === 'needs_review';
    }
}
