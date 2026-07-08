<?php

namespace Rushing\CompositionSpineData\Vocabulary;

use Rushing\CompositionSpineData\GenerationAttributesStrategy;
use Rushing\CompositionSpineData\KeywordVocabulary;

/**
 * One generation keyword, declared once by its owner. It names the {@see KeywordVocabulary}
 * accessor that yields the keyword string (so the configured prefix stays correct) and a *reference* to where
 * its value domain is reflected from — an enum class, a method return type, a constructor — never a literal
 * value schema. Both the emit path (via {@see GenerationAttributesStrategy})
 * and the describe path ({@see GrammarVocabulary}) read these, so the two can never drift.
 */
class GenerationKeyword
{
    /**
     * @param  string  $accessor  the KeywordVocabulary method that yields this keyword (e.g. 'beat')
     * @param  ValueSource  $source  how the value domain is reflected
     * @param  string  $description  one line, owned here by the keyword's owner
     * @param  class-string|null  $sourceClass  the enum / ctor / method-owning class the value is reflected from
     * @param  string|null  $sourceMethod  the method whose return type is reflected (ValueSource::Union)
     * @param  string|null  $tsType  a named TypeScript type to emit for this value (enums, objects)
     */
    public function __construct(
        public string $accessor,
        public ValueSource $source,
        public string $description,
        public ?string $sourceClass = null,
        public ?string $sourceMethod = null,
        public ?string $tsType = null,
    ) {}
}
