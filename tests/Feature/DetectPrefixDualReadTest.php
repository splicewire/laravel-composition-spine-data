<?php

use Splicewire\Composition\KeywordVocabulary;

/**
 * RCH-34 dual-read guard. The keyword vocabulary graduated per-engine prefixes to the
 * per-PRODUCT-TIER seam (ADR-0092): composition's engine-private keywords now emit
 * `x-splice-*` (was legacy `x-swc-*`; and older schemas carry the bare, empty-prefix
 * `x-beat`). Persisted schemas authored under any of those prefixes must STILL be
 * interpretable — {@see KeywordVocabulary::forSchema()} routes through the private
 * `detectPrefix()`, which must read the OLD prefixes AND the new one. RCH-34 is
 * dual-read only: nothing old is deleted or migrated, so this window must hold.
 *
 * `forSchema()` is the public seam over `detectPrefix()`: the detected prefix surfaces
 * as the returned vocabulary's `->prefix`, so asserting on it proves the detection.
 */
it('reads the current x-splice-* prefix', function () {
    $schema = ['type' => 'object', 'x-splice-beat' => ['only' => 'A beat.']];

    expect(KeywordVocabulary::forSchema($schema)->prefix)->toBe('splice')
        ->and(KeywordVocabulary::forSchema($schema)->beat())->toBe('x-splice-beat');
});

it('still reads the legacy x-swc-* prefix (dual-read window)', function () {
    $schema = ['type' => 'object', 'x-swc-beat' => ['only' => 'A beat.']];

    expect(KeywordVocabulary::forSchema($schema)->prefix)->toBe('swc')
        ->and(KeywordVocabulary::forSchema($schema)->beat())->toBe('x-swc-beat');
});

it('still reads the oldest bare (empty-prefix) x-beat keyword', function () {
    $schema = ['type' => 'object', 'x-beat' => ['only' => 'A beat.']];

    expect(KeywordVocabulary::forSchema($schema)->prefix)->toBe('')
        ->and(KeywordVocabulary::forSchema($schema)->beat())->toBe('x-beat');
});

it('detects the prefix from a nested beat keyword, not just the top level', function () {
    $legacy = ['type' => 'object', 'properties' => ['body' => ['x-swc-beat' => ['only' => 'A.']]]];
    $current = ['type' => 'object', 'properties' => ['body' => ['x-splice-beat' => ['only' => 'A.']]]];

    expect(KeywordVocabulary::forSchema($legacy)->prefix)->toBe('swc')
        ->and(KeywordVocabulary::forSchema($current)->prefix)->toBe('splice');
});

it('falls back to the shared vocabulary when no beat keyword is present', function () {
    // Nothing to detect -> shared() default prefix (splice), not a crash.
    expect(KeywordVocabulary::forSchema(['type' => 'object'])->prefix)->toBe('splice');
});
