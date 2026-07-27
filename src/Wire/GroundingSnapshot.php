<?php

namespace Splicewire\Composition\Wire;

/**
 * Shared spine wire DTO (ADR-0093 tier 5).
 *
 * Outbound grounding snapshot the antenna returns to the platform: the grouped
 * `['type' => [{token, ...}, ...], ...]` shape the interpreter drains. A remotely pulled
 * snapshot is interchangeable with a local or inline-pushed one.
 */
class GroundingSnapshot
{
    /**
     * @param  array<string, array<int, array<string, mixed>>>  $groups
     */
    public function __construct(
        public array $groups = [],
    ) {}

    /**
     * @param  array<string, array<int, array<string, mixed>>>  $groups
     */
    public static function fromArray(array $groups): self
    {
        return new static($groups);
    }

    /**
     * Add (or append to) a group of grounding records keyed by type.
     *
     * @param  array<int, array<string, mixed>>  $records
     */
    public function withGroup(string $type, array $records): self
    {
        $groups = $this->groups;
        $groups[$type] = array_merge($groups[$type] ?? [], $records);

        return new static($groups);
    }

    /**
     * The grouped array shape the webhook responds with.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function toArray(): array
    {
        return $this->groups;
    }
}
