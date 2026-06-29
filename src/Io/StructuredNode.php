<?php

declare(strict_types=1);

namespace Rushing\CompositionSpineData\Io;

/**
 * One node of a {@see StructuredExport} reconstructed into the recursive antenna
 * grid. The platform emits nodes flat with a dotted positional `path` (root nodes
 * `"0","1"`; a child of `"1"` is `"1.0"`); {@see self::tree()} rebuilds the
 * hierarchy from those paths so the antenna can render a nested structure without
 * the platform shipping nested JSON.
 *
 * `children` is intentionally mutable so reconstruction can attach descendants;
 * the leaf payload (`type`/`fields`/`groundingTokens`/`path`) is read-only.
 */
final class StructuredNode
{
    /**
     * @param  array<string, mixed>  $fields
     * @param  array<int, string>  $groundingTokens
     * @param  array<int, StructuredNode>  $children
     */
    public function __construct(
        public readonly ?string $type,
        public readonly array $fields,
        public readonly array $groundingTokens,
        public readonly string $path,
        public array $children = [],
    ) {}

    /**
     * Reconstruct the forest of root nodes from a flat node list, nesting each node
     * under the node whose `path` is its dotted prefix. A node whose parent path is
     * absent is treated as a root (tolerant of partial exports). Document order is
     * preserved among siblings.
     *
     * @param  array<int, array{type: ?string, fields: array<string, mixed>, groundingTokens: array<int, string>, path: string}>  $nodes
     * @return array<int, StructuredNode>
     */
    public static function tree(array $nodes): array
    {
        /** @var array<string, StructuredNode> $byPath */
        $byPath = [];
        foreach ($nodes as $node) {
            $byPath[$node['path']] = new self(
                type: $node['type'],
                fields: $node['fields'],
                groundingTokens: $node['groundingTokens'],
                path: $node['path'],
            );
        }

        $roots = [];
        foreach ($byPath as $path => $node) {
            // Numeric-string array keys ("0") are coerced to int by PHP; normalize.
            $path = (string) $path;
            $pos = strrpos($path, '.');
            $parentPath = $pos === false ? null : substr($path, 0, $pos);

            if ($parentPath !== null && isset($byPath[$parentPath])) {
                $byPath[$parentPath]->children[] = $node;
            } else {
                $roots[] = $node;
            }
        }

        return $roots;
    }

    /**
     * Hydrate this node's generated `fields` into one of our 1:1 `spatie/laravel-data`
     * Data classes — the same authored leaf type the schema was projected from. This is
     * the drift-free seam: the class that authors the generation schema also receives the
     * result, so a satellite consuming the client never re-types the payload by hand.
     *
     * @template T of object
     *
     * @param  class-string<T>  $dataClass  a spatie laravel-data Data class
     * @return T
     */
    public function hydrate(string $dataClass): object
    {
        return $dataClass::from($this->fields);
    }
}
