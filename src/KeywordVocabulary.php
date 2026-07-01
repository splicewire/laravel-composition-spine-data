<?php

declare(strict_types=1);

namespace Rushing\CompositionSpineData;

/**
 * The single chokepoint for this engine's schema keywords. Both the emit side (schema generation /
 * grammar) and every read site (the interpreter's grammar walk, prose discipline) route through one
 * instance, so a keyword name is defined in exactly one place.
 *
 * Two tiers, one rule:
 *  - ENGINE-PRIVATE keywords are namespaced `x-{prefix}-*` (default `swc`), keeping them out of the
 *    shared OpenAPI `x-` commons. Each engine owns its own prefix (composition `swc`, knowledge `swk`),
 *    which falls out for free because each engine ships its own spine-data package.
 *  - BASE/STANDARD vocabulary (`@id`, `x-dereference`) is NOT prefixed — like JSON Schema's `$ref` it is
 *    base-owned (canonical home: the `rushing/laravel-json-reference` leaf) and means the same thing
 *    across every engine. A cross-engine handle only works if the keyword carrying it is unprefixed.
 */
final class KeywordVocabulary
{
    public function __construct(public readonly string $prefix = 'swc') {}

    /**
     * The shared, config-driven vocabulary: the container singleton when one is bound (so every emit and
     * read site agrees on the prefix), else a default-prefix instance for bare unit contexts.
     */
    public static function shared(): self
    {
        if (function_exists('app')) {
            try {
                if (app()->bound(self::class)) {
                    return app(self::class);
                }
            } catch (\Throwable) {
                // No container reachable — fall through to the default prefix.
            }
        }

        return new self;
    }

    public function beat(): string
    {
        return $this->engine('beat');
    }

    public function cache(): string
    {
        return $this->engine('cache');
    }

    public function generate(): string
    {
        return $this->engine('generate');
    }

    public function ground(): string
    {
        return $this->engine('ground');
    }

    public function maxDepth(): string
    {
        return $this->engine('max-depth');
    }

    public function pause(): string
    {
        return $this->engine('pause');
    }

    public function prose(): string
    {
        return $this->engine('prose');
    }

    public function proseNote(): string
    {
        return $this->engine('prose-note');
    }

    /**
     * The reserved, UNPREFIXED dereference dispatch keyword (base/standard vocabulary). Named here so the
     * read-side verb has a home from day one; its handler dispatch is wired by the dereference-seam issue.
     */
    public function dereference(): string
    {
        return 'x-dereference';
    }

    private function engine(string $name): string
    {
        return "x-{$this->prefix}-{$name}";
    }
}
