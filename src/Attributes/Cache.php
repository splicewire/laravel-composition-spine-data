<?php

declare(strict_types=1);

namespace Rushing\CompositionSpineData\Attributes;

use Attribute;

/**
 * Declares a caching policy for a Beat's generate/ground capability (ADR-0039 declarative caching).
 * Projected to the `x-cache` JSON-Schema keyword by GenerationAttributesStrategy and stripped by
 * forLlmStrict, so the policy travels with the shipped schema yet never reaches the model. The engine
 * reads `x-cache` and wraps the resolved invocable in a CachedInvocable — caching is a decorator,
 * orthogonal to the invocable's binding (local/mcp/webhook).
 *
 * Two scopes (the real axis is lifetime, not remote-vs-local):
 *  - `invocation` — input-keyed with a TTL; a memoized capability call shared across compositions.
 *  - `snapshot`   — frozen for the same inputs with no expiry; the grounding-freeze affordance.
 *
 * `key` optionally narrows the cache key to a named subset of the grounding snapshot (omitted → the
 * whole snapshot participates), so unrelated grounding changes don't needlessly bust the entry.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
final class Cache
{
    /**
     * @param  'invocation'|'snapshot'  $scope
     * @param  int|null  $ttl  seconds; null uses the scope default (invocation → config TTL, snapshot → no expiry)
     * @param  array<int, string>  $key  optional grounding-key subset the cache key is derived from
     */
    public function __construct(
        public readonly string $scope = 'invocation',
        public readonly ?int $ttl = null,
        public readonly array $key = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function keyword(): array
    {
        return array_filter(
            ['scope' => $this->scope, 'ttl' => $this->ttl, 'key' => $this->key],
            fn ($value) => $value !== null && $value !== [],
        );
    }
}
