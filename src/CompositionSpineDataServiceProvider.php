<?php

declare(strict_types=1);

namespace Rushing\CompositionSpineData;

use Illuminate\Support\ServiceProvider;

/**
 * Registers the composition generation-attributes strategy into the
 * laravel-data-schemas pipeline so `#[Beat]`/`#[Ground]`/`#[Generate]`/`#[Prose]`/
 * `#[Pause]` project to the `x-*` vendor keywords. Idempotent — the strategy is
 * appended once regardless of how many spine-aware packages boot.
 *
 * This is the single owner of that registration: the engine no longer registers it
 * (the attributes + strategy live here now), and any host or satellite that depends
 * on this package — directly or transitively through the client — gets the same one
 * read path for the generation grammar with no drift.
 */
class CompositionSpineDataServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $strategies = config('data-schemas.strategies', []);

        if (! in_array(GenerationAttributesStrategy::class, $strategies, true)) {
            $strategies[] = GenerationAttributesStrategy::class;
            config(['data-schemas.strategies' => $strategies]);
        }
    }
}
