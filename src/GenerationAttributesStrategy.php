<?php

declare(strict_types=1);

namespace Rushing\CompositionSpineData;

use ReflectionClass;
use ReflectionProperty;
use Rushing\CompositionSpineData\Attributes\Beat;
use Rushing\CompositionSpineData\Attributes\Cache;
use Rushing\CompositionSpineData\Attributes\EmbedPalette;
use Rushing\CompositionSpineData\Attributes\Generate;
use Rushing\CompositionSpineData\Attributes\Ground;
use Rushing\CompositionSpineData\Attributes\Pause;
use Rushing\CompositionSpineData\Attributes\Polish;
use Rushing\CompositionSpineData\Attributes\Prose;
use Rushing\LaravelDataSchemas\Generators\JsonSchemaGenerator;
use Rushing\LaravelDataSchemas\Strategies\SchemaStrategy;
use Rushing\LaravelDataSchemas\Strategies\SchemaStrategyContext;

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
final class GenerationAttributesStrategy implements SchemaStrategy
{
    private readonly KeywordVocabulary $vocab;

    public function __construct(?KeywordVocabulary $vocab = null)
    {
        $this->vocab = $vocab ?? KeywordVocabulary::shared();
    }

    public function apply(ReflectionProperty $property, array $schema, SchemaStrategyContext $context): array
    {
        if ($beat = $this->firstAttribute($property, Beat::class)) {
            $schema[$this->vocab->beat()] = $beat->kind->value;
        }

        if ($ground = $this->firstAttribute($property, Ground::class)) {
            $schema[$this->vocab->ground()] = $ground->keyword();
        }

        if ($generate = $this->firstAttribute($property, Generate::class)) {
            $schema[$this->vocab->generate()] = $generate->keyword();
        }

        if ($prose = $this->firstAttribute($property, Prose::class)) {
            $schema[$this->vocab->prose()] = $prose->role->value;
            if ($prose->note !== null) {
                $schema[$this->vocab->proseNote()] = $prose->note;
            }
        }

        if ($pause = $this->firstAttribute($property, Pause::class)) {
            $schema[$this->vocab->pause()] = $pause->enabled;
        }

        if ($polish = $this->firstAttribute($property, Polish::class)) {
            $schema[$this->vocab->polish()] = $polish->auto;
        }

        if ($cache = $this->firstAttribute($property, Cache::class)) {
            $schema[$this->vocab->cache()] = $cache->keyword();
        }

        if ($palette = $this->firstAttribute($property, EmbedPalette::class)) {
            $schema = $this->applyEmbedPalette($schema, $palette);
        }

        return $schema;
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
