<?php

use Rushing\CompositionSpineData\Attributes\Grounding;
use Rushing\CompositionSpineData\GenerationAttributesStrategy;
use Rushing\CompositionSpineData\KeywordVocabulary;
use Rushing\LaravelDataSchemas\Generators\JsonSchemaGenerator;
use Spatie\LaravelData\Data;

/**
 * Grounding-fusion issue 03 — the `Grounding` attribute projects declared grounding SOURCES to the
 * `x-swc-grounding` keyword through the shared bindings (mirrors the class-level beat-grammar
 * projection guard): class level for node/root declarations, property level for beat supplements.
 */
#[Grounding(sources: [['context_scope' => 'brand-voice'], ['webhook' => ['endpoint' => 'https://example.test/catalog']]])]
class RootGroundingFixture extends Data
{
    public function __construct(
        public string $body = '',
    ) {}
}

#[Grounding(sources: [['facts' => ['notes' => []]]], fusion: 'reconcile')]
class NamedFusionFixture extends Data
{
    public function __construct(
        public string $body = '',
    ) {}
}

class SupplementGroundingFixture extends Data
{
    public function __construct(
        #[Grounding(sources: [['context_scope' => ['silos' => ['background']]]])]
        public array $sections = [],
    ) {}
}

it('projects a class-level Grounding declaration to x-swc-grounding through the shared class-keyword path', function () {
    $vocab = KeywordVocabulary::shared();
    $keywords = GenerationAttributesStrategy::classKeywords(new ReflectionClass(RootGroundingFixture::class));

    expect($keywords[$vocab->grounding()])->toBe([
        ['context_scope' => 'brand-voice'],
        ['webhook' => ['endpoint' => 'https://example.test/catalog']],
    ]);
});

it('projects the {fusion, sources} object form when a strategy is named — single-sourced through keyword()', function () {
    $vocab = KeywordVocabulary::shared();
    $keywords = GenerationAttributesStrategy::classKeywords(new ReflectionClass(NamedFusionFixture::class));

    expect($keywords[$vocab->grounding()])->toBe(
        (new Grounding(sources: [['facts' => ['notes' => []]]], fusion: 'reconcile'))->keyword(),
    )->and($keywords[$vocab->grounding()]['fusion'])->toBe('reconcile');
});

it('projects a property-level Grounding supplement onto the field schema', function () {
    $vocab = KeywordVocabulary::shared();
    $schema = (new JsonSchemaGenerator)->generate(new ReflectionClass(SupplementGroundingFixture::class));

    expect($schema['properties']['sections'][$vocab->grounding()])
        ->toBe([['context_scope' => ['silos' => ['background']]]]);
});

it('names the keyword through the vocabulary (prefix-agnostic)', function () {
    expect(KeywordVocabulary::shared()->grounding())->toBe('x-'.KeywordVocabulary::shared()->prefix.'-grounding')
        ->and((new KeywordVocabulary('alt'))->grounding())->toBe('x-alt-grounding');
});
