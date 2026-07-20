<?php

namespace Splicewire\CompositionSpineData\Wire;

/**
 * Shared spine wire DTO (ADR-0093 tier 5).
 *
 * Typed view over one attached media record from the fragments attach envelope
 * (`{data: {media: [{uuid, file_name, original_url, ...}]}}`). The uuid is the reference cells
 * carry (e.g. a music cell's melody_ref); the full record stays in {@see $attributes}.
 */
class MediaRef
{
    /**
     * @param  array<string, mixed>  $attributes  the raw media record.
     */
    public function __construct(
        public string $uuid,
        public ?string $fileName = null,
        public ?string $contentUrl = null,
        public array $attributes = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data  one unwrapped media record.
     */
    public static function fromArray(array $data): self
    {
        return new static(
            uuid: (string) ($data['uuid'] ?? ''),
            fileName: isset($data['file_name']) ? (string) $data['file_name']
                : (isset($data['fileName']) ? (string) $data['fileName'] : null),
            // The media library serves the file as original_url; contentUrl is the
            // schema.org-flavoured twin some surfaces emit.
            contentUrl: isset($data['original_url']) ? (string) $data['original_url']
                : (isset($data['contentUrl']) ? (string) $data['contentUrl'] : null),
            attributes: $data,
        );
    }
}
