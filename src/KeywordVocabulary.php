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

    /**
     * The vocabulary that AUTHORED a persisted schema, detected from its own keyword keys. A stored antenna
     * schema is only interpretable by the vocab that wrote it — keyword prefixes change over time (a legacy
     * schema carries bare `x-beat`, a current one `x-swc-beat`), so re-derivation (regenerate / refine)
     * must read a schema through its OWN prefix, not the reader's active one. Falls back to {@see shared()}
     * when no engine keyword is present (nothing to detect).
     *
     * @param  array<string, mixed>  $schema  a collapsed JSON Schema carrying `x-*` keywords
     */
    public static function forSchema(array $schema): self
    {
        $prefix = self::detectPrefix($schema);

        return $prefix === null ? self::shared() : new self($prefix);
    }

    /**
     * The keyword prefix a schema was authored with, found by scanning (recursively) for its `beat`
     * keyword: bare `x-beat` → the legacy empty prefix (`''`); `x-swc-beat` → `swc`. Returns null when no
     * beat keyword is present.
     *
     * @param  array<string, mixed>  $schema
     */
    private static function detectPrefix(array $schema): ?string
    {
        foreach ($schema as $key => $value) {
            if (is_string($key)) {
                if ($key === 'x-beat') {
                    return '';
                }
                if (preg_match('/^x-(.+)-beat$/', $key, $m) === 1) {
                    return $m[1];
                }
            }

            if (is_array($value)) {
                $nested = self::detectPrefix($value);
                if ($nested !== null) {
                    return $nested;
                }
            }
        }

        return null;
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
        return $this->prefix === ''
            ? "x-{$name}"
            : "x-{$this->prefix}-{$name}";
    }
}
