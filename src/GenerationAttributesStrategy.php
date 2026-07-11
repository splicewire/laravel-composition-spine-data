<?php

namespace Rushing\CompositionSpineData;

use ReflectionClass;
use ReflectionProperty;
use Rushing\CompositionSpineData\Attributes\Beat;
use Rushing\CompositionSpineData\Attributes\BeatKind;
use Rushing\CompositionSpineData\Attributes\Cache;
use Rushing\CompositionSpineData\Attributes\EmbedPalette;
use Rushing\CompositionSpineData\Attributes\Generate;
use Rushing\CompositionSpineData\Attributes\Ground;
use Rushing\CompositionSpineData\Attributes\Pause;
use Rushing\CompositionSpineData\Attributes\Polish;
use Rushing\CompositionSpineData\Attributes\Prose;
use Rushing\CompositionSpineData\Attributes\ProseRole;
use Rushing\CompositionSpineData\Attributes\Repeat;
use Rushing\CompositionSpineData\Vocabulary\GrammarVocabulary;
use Rushing\LaravelDataSchemas\Generators\JsonSchemaGenerator;
use Rushing\LaravelDataSchemas\Strategies\SchemaStrategy;
use Rushing\LaravelDataSchemas\Strategies\SchemaStrategyContext;
use Rushing\LaravelDataSchemas\Vocabulary\AttributeBinding;
use Rushing\LaravelDataSchemas\Vocabulary\KeywordDescriptor;
use Rushing\LaravelDataSchemas\Vocabulary\ValueSource;

/**
 * Projects the composition generation attributes (`#[Beat]`, `#[Ground]`,
 * `#[Generate]`, `#[Prose]`, `#[Pause]`) onto a property schema as the
 * `x-beat`/`x-ground`/`x-generate`/`x-prose`/`x-pause` vendor keywords. The
 * interpreter reads these keywords — never the PHP attributes — so any schema
 * origin (local PHP, future remote codegen) converges on one read path.
 * `forLlmStrict` strips them, so they never reach the model-facing contract.
 *
 * Registered into `config('data-schemas.strategies')` by this package's service
 * provider; it contributes nothing to a property without these attributes.
 */
class GenerationAttributesStrategy implements SchemaStrategy
{
    private KeywordVocabulary $vocab;

    public function __construct(?KeywordVocabulary $vocab = null)
    {
        $this->vocab = $vocab ?? KeywordVocabulary::shared();
    }

    public function apply(ReflectionProperty $property, array $schema, SchemaStrategyContext $context): array
    {
        foreach (self::bindings() as $binding) {
            if ($attr = $this->firstAttribute($property, $binding->attributeClass)) {
                $schema = ($binding->emit)($attr, $this->vocab, $schema);
            }
        }

        // EmbedPalette is a structural branch, not a keyword: it reshapes `items`, stamps no
        // `x-*` keyword, and so is NOT part of the generation-keyword vocabulary.
        if ($palette = $this->firstAttribute($property, EmbedPalette::class)) {
            $schema = $this->applyEmbedPalette($schema, $palette);
        }

        return $schema;
    }

    /**
     * The single declared attribute↔keyword bindings, consumed by both {@see apply()} (emit) and
     * {@see GrammarVocabulary} (describe). Each binding names the
     * attribute, the closure that stamps its keyword(s), and the keyword descriptor(s) it contributes — the
     * value domain of each keyword is a *reference* (an enum, a method return type, a ctor), reflected at
     * describe time, never a literal here.
     *
     * @return list<AttributeBinding>
     */
    public static function bindings(): array
    {
        return [
            new AttributeBinding(
                Beat::class,
                function (Beat $attr, KeywordVocabulary $vocab, array $schema): array {
                    $schema[$vocab->beat()] = $attr->kind->value;

                    return $schema;
                },
                [new KeywordDescriptor(
                    accessor: 'beat',
                    source: ValueSource::Enum,
                    description: 'How the interpreter treats the beat at the frontier: expandable → one focused expansion call realizes its children; writable → the model writes the leaf directly.',
                    sourceClass: BeatKind::class,
                    tsType: 'BeatKind',
                )],
            ),
            new AttributeBinding(
                Ground::class,
                function (Ground $attr, KeywordVocabulary $vocab, array $schema): array {
                    $schema[$vocab->ground()] = $attr->keyword();

                    return $schema;
                },
                [new KeywordDescriptor(
                    accessor: 'ground',
                    source: ValueSource::Union,
                    description: 'Fill this property from the Composition grounding snapshot: a source name selects a registered ground capability, or `true` uses the property name as the source.',
                    sourceClass: Ground::class,
                    sourceMethod: 'keyword',
                )],
            ),
            new AttributeBinding(
                Generate::class,
                function (Generate $attr, KeywordVocabulary $vocab, array $schema): array {
                    $schema[$vocab->generate()] = $attr->keyword();

                    return $schema;
                },
                [new KeywordDescriptor(
                    accessor: 'generate',
                    source: ValueSource::Union,
                    description: 'Generate this property when its beat is expanded: a handler name selects a registered generate capability, or `true` uses the profile default.',
                    sourceClass: Generate::class,
                    sourceMethod: 'keyword',
                )],
            ),
            new AttributeBinding(
                Prose::class,
                function (Prose $attr, KeywordVocabulary $vocab, array $schema): array {
                    $schema[$vocab->prose()] = $attr->role->value;
                    if ($attr->note !== null) {
                        $schema[$vocab->proseNote()] = $attr->note;
                    }

                    return $schema;
                },
                [
                    new KeywordDescriptor(
                        accessor: 'prose',
                        source: ValueSource::Enum,
                        description: 'The prose disposition of a grounding field — how its facts may be discussed in body prose (subject → write freely; render-only → do not name; nameable → may be named).',
                        sourceClass: ProseRole::class,
                        tsType: 'ProseRole',
                    ),
                    new KeywordDescriptor(
                        accessor: 'proseNote',
                        source: ValueSource::Text,
                        description: 'An optional field-level prose instruction that accompanies the prose disposition.',
                    ),
                ],
            ),
            new AttributeBinding(
                Pause::class,
                function (Pause $attr, KeywordVocabulary $vocab, array $schema): array {
                    $schema[$vocab->pause()] = $attr->enabled;

                    return $schema;
                },
                [new KeywordDescriptor(
                    accessor: 'pause',
                    source: ValueSource::Boolean,
                    description: 'Marks the beat a pause checkpoint (HITL): the interpreter yields its cell for review and does not expand its children until approved.',
                )],
            ),
            new AttributeBinding(
                Polish::class,
                function (Polish $attr, KeywordVocabulary $vocab, array $schema): array {
                    $schema[$vocab->polish()] = $attr->auto;

                    return $schema;
                },
                [new KeywordDescriptor(
                    accessor: 'polish',
                    source: ValueSource::Boolean,
                    description: 'Whether the beat is eligible for auto-polish; `false` fences its cells out of the whole-composition polish orchestrator.',
                )],
            ),
            new AttributeBinding(
                Cache::class,
                function (Cache $attr, KeywordVocabulary $vocab, array $schema): array {
                    $schema[$vocab->cache()] = $attr->keyword();

                    return $schema;
                },
                [new KeywordDescriptor(
                    accessor: 'cache',
                    source: ValueSource::Object_,
                    description: 'A caching policy for the beat generate/ground capability: `scope` (invocation → TTL-keyed; snapshot → frozen), optional `ttl`, and an optional grounding-key subset.',
                    sourceClass: Cache::class,
                    tsType: 'GenerationCachePolicy',
                )],
            ),
            new AttributeBinding(
                Repeat::class,
                function (Repeat $attr, KeywordVocabulary $vocab, array $schema): array {
                    $schema[$vocab->repeat()] = $attr->keyword();

                    return $schema;
                },
                [new KeywordDescriptor(
                    accessor: 'repeat',
                    source: ValueSource::Union,
                    description: 'Reprise a named sibling beat instead of generating fresh: a bare string reprises it verbatim; a `{of, vary}` object revises the source to satisfy `vary` while preserving its structure and grounding tokens.',
                    sourceClass: Repeat::class,
                    sourceMethod: 'keyword',
                )],
            ),
        ];
    }

    /**
     * Project an `#[EmbedPalette([A::class, ...])]` array property: build each listed embed
     * `Data` class's item schema (a `type` discriminator `const` + its own model-facing prose
     * fields) and place them under the array's `items`.
     *
     * ONE type  → `items: <that type's item schema>`.
     * N>1 types → `items: {anyOf: [<each type's item schema>]}`.
     *
     * The single-type case avoids `oneOf`/`anyOf`-in-array (OpenAI strict structured output
     * rejects it). Strict compatibility for the multi-type `anyOf` branch is UNVERIFIED — a
     * later issue's concern; the shape is designed for it but not exercised by a strict provider.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    private function applyEmbedPalette(array $schema, EmbedPalette $palette): array
    {
        $schema['type'] = 'array';

        $items = array_map(
            fn (string $type) => $this->embedItemSchema($type),
            array_values($palette->types),
        );

        $schema['items'] = count($items) === 1 ? $items[0] : ['anyOf' => $items];

        return $schema;
    }

    /**
     * Build one embed type's item object schema: the embed `Data`'s generated properties (its
     * editorial prose + grounding-token citations, projected with x-* via this same strategy)
     * plus a leading `type` discriminator field whose value is the class's `EMBED_TYPE` const,
     * emitted as a `const` so a persisted entry is self-identifying. ArticleGrammar's `harden()`
     * later stamps additionalProperties:false + required on this object like the rest of the tree.
     *
     * Inlined (no `$ref`/`$defs`): a flat embed item has no further nested `Data`, so the
     * generated schema is self-contained.
     *
     * @param  class-string  $type
     * @return array<string, mixed>
     */
    private function embedItemSchema(string $type): array
    {
        $generated = (new JsonSchemaGenerator)->generate(new ReflectionClass($type));

        $properties = is_array($generated['properties'] ?? null) ? $generated['properties'] : [];

        // Drop the persisted `type` discriminator property if the class declares one; we re-emit
        // it below as a fixed `const` so the model can only return the embed's true identity.
        unset($properties['type']);

        $discriminator = defined("$type::EMBED_TYPE") ? constant("$type::EMBED_TYPE") : null;

        $item = ['type' => 'object'];
        if ($discriminator !== null) {
            $item['properties']['type'] = ['type' => 'string', 'const' => $discriminator];
        }
        foreach ($properties as $key => $value) {
            $item['properties'][$key] = $value;
        }

        return $item;
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $attribute
     * @return T|null
     */
    private function firstAttribute(ReflectionProperty $property, string $attribute): ?object
    {
        $attrs = $property->getAttributes($attribute);

        return empty($attrs) ? null : $attrs[0]->newInstance();
    }
}
