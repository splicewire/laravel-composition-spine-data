<?php

declare(strict_types=1);

namespace Rushing\CompositionSpineData;

use ReflectionProperty;
use Rushing\CompositionSpineData\Attributes\Beat;
use Rushing\CompositionSpineData\Attributes\Generate;
use Rushing\CompositionSpineData\Attributes\Ground;
use Rushing\CompositionSpineData\Attributes\Pause;
use Rushing\CompositionSpineData\Attributes\Prose;
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
    public function apply(ReflectionProperty $property, array $schema, SchemaStrategyContext $context): array
    {
        if ($beat = $this->firstAttribute($property, Beat::class)) {
            $schema['x-beat'] = $beat->kind->value;
        }

        if ($ground = $this->firstAttribute($property, Ground::class)) {
            $schema['x-ground'] = $ground->keyword();
        }

        if ($generate = $this->firstAttribute($property, Generate::class)) {
            $schema['x-generate'] = $generate->keyword();
        }

        if ($prose = $this->firstAttribute($property, Prose::class)) {
            $schema['x-prose'] = $prose->role->value;
            if ($prose->note !== null) {
                $schema['x-prose-note'] = $prose->note;
            }
        }

        if ($pause = $this->firstAttribute($property, Pause::class)) {
            $schema['x-pause'] = $pause->enabled;
        }

        return $schema;
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
