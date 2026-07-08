<?php

namespace Rushing\CompositionSpineData\Vocabulary;

use Closure;
use Rushing\CompositionSpineData\GenerationAttributesStrategy;
use Rushing\CompositionSpineData\KeywordVocabulary;

/**
 * Binds one generation attribute (`#[Beat]`, `#[Ground]`, …) to the keyword(s) it contributes and the
 * closure that stamps them onto a property schema. The single declared source consumed by BOTH
 * {@see GenerationAttributesStrategy::apply()} (emit) and
 * {@see GrammarVocabulary} (describe), so the emitted keywords and the described vocabulary cannot diverge.
 */
class AttributeBinding
{
    /**
     * @param  class-string  $attributeClass  the attribute this binding projects
     * @param  Closure(object, KeywordVocabulary, array<string, mixed>): array<string, mixed>  $emit
     *                                                                                                stamps this attribute's keyword(s) onto the property schema
     * @param  list<GenerationKeyword>  $keywords  the keyword(s) this attribute contributes to the vocabulary
     */
    public function __construct(
        public string $attributeClass,
        public Closure $emit,
        public array $keywords,
    ) {}
}
