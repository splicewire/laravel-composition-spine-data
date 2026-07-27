<?php

namespace Splicewire\Composition\Wire;

/**
 * Shared spine wire DTO (ADR-0093 tier 5).
 *
 * Typed view over the admin tenant JSON envelope (`{data: {...tenant...}}`) returned by the
 * brokered-provisioning surface (`/api/admin/tenants`). A Brokered Tenant is an ordinary tenant
 * attributed to a broker via `parent_tenant_id` (ADR-0043); only the fields the provision/track
 * flow needs are surfaced — the rest stays in {@see $attributes}.
 */
class Tenant
{
    /**
     * @param  array<string, mixed>  $attributes  the raw `data` record.
     */
    public function __construct(
        public string $id,
        public string $slug,
        public ?string $status = null,
        public ?string $parentTenantId = null,
        public ?string $host = null,
        public array $attributes = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data  the unwrapped `data` record.
     */
    public static function fromArray(array $data): self
    {
        return new static(
            id: (string) ($data['id'] ?? ''),
            slug: (string) ($data['slug'] ?? ($data['id'] ?? '')),
            status: isset($data['provisioningStatus'])
                ? (string) $data['provisioningStatus']
                : (isset($data['status']) ? (string) $data['status'] : null),
            parentTenantId: isset($data['parentTenantId']) ? (string) $data['parentTenantId'] : null,
            host: isset($data['primaryHost'])
                ? (string) $data['primaryHost']
                : (isset($data['host']) ? (string) $data['host'] : null),
            attributes: $data,
        );
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
