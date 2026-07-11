<?php

use Rushing\CompositionSpineData\Attributes\Cache;
use Rushing\CompositionSpineData\KeywordVocabulary;
use Rushing\CompositionSpineData\Schema\BeatGrammar;
use Rushing\CompositionSpineData\Tests\Stubs\ClassLevelBeatData;

/**
 * Issue 03 (Bug A) — BeatGrammar used to read only class-level Beat/Generate/Ground and silently
 * DROP class-level Pause/Polish/Cache/Repeat/MaxDepth (all declared TARGET_CLASS, all wired for
 * properties). These assert the class-level projection is now complete AND single-sourced through
 * the same GenerationAttributesStrategy bindings the property path uses.
 */
function classLevelBeatNode(): array
{
    $schema = BeatGrammar::for(ClassLevelBeatData::class)
        ->title('ClassLevel')
        ->beat('only', 'A beat.')
        ->build();

    return $schema['properties']['only'];
}

it('projects the always-read class-level keywords (beat/generate/ground) in their historical order', function () {
    $vocab = KeywordVocabulary::shared();
    $node = classLevelBeatNode();

    expect($node[$vocab->beat()])->toBe('expandable')
        ->and($node[$vocab->generate()])->toBeTrue()
        ->and($node[$vocab->ground()])->toBeTrue();

    // beat → generate → ground stay first and in order (the fixture-pinned wrapper shape).
    $keys = array_keys($node);
    expect(array_slice($keys, 0, 4))->toBe(['type', $vocab->beat(), $vocab->generate(), $vocab->ground()]);
});

it('now projects the previously-dropped class-level keywords (pause/polish/cache/max-depth)', function () {
    $vocab = KeywordVocabulary::shared();
    $node = classLevelBeatNode();

    expect($node[$vocab->pause()])->toBeTrue()
        ->and($node[$vocab->polish()])->toBeFalse()
        ->and($node[$vocab->cache()])->toBe(['scope' => 'snapshot'])
        ->and($node[$vocab->maxDepth()])->toBe(3);
});

it('single-sources the class-level values through the same bindings as the property path', function () {
    // The emitted value must equal what the binding's own emit closure produces — proving the class
    // path is not a divergent hand-rolled projection. Cache proves it: the {scope} object shape comes
    // from Cache::keyword(), never a literal in BeatGrammar.
    $vocab = KeywordVocabulary::shared();
    $node = classLevelBeatNode();

    expect($node[$vocab->cache()])->toBe((new Cache(scope: 'snapshot'))->keyword());
});
