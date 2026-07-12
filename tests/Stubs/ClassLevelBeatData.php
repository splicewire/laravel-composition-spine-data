<?php

namespace Splicewire\CompositionSpineData\Tests\Stubs;

use Splicewire\CompositionSpineData\Attributes\Beat;
use Splicewire\CompositionSpineData\Attributes\BeatKind;
use Splicewire\CompositionSpineData\Attributes\Cache;
use Splicewire\CompositionSpineData\Attributes\Generate;
use Splicewire\CompositionSpineData\Attributes\Ground;
use Splicewire\CompositionSpineData\Attributes\MaxDepth;
use Splicewire\CompositionSpineData\Attributes\Pause;
use Splicewire\CompositionSpineData\Attributes\Polish;
use Splicewire\CompositionSpineData\Attributes\Prose;
use Splicewire\CompositionSpineData\Attributes\ProseRole;
use Spatie\LaravelData\Data;

/**
 * A leaf declaring the trailing generation attributes at CLASS level — the ones BeatGrammar
 * used to silently drop (`#[Pause]`/`#[Polish]`/`#[Cache]`/`#[MaxDepth]`) alongside the
 * always-read `#[Beat]`/`#[Generate]`/`#[Ground]`. Proves the class-level projection is now
 * complete and single-sourced through the same bindings the property level uses.
 */
#[Beat(BeatKind::Expandable)]
#[Generate]
#[Ground]
#[Pause]
#[Polish(auto: false)]
#[Cache(scope: 'snapshot')]
#[MaxDepth(3)]
class ClassLevelBeatData extends Data
{
    public function __construct(
        #[Prose(ProseRole::Subject)]
        public string $interpretation = '',
    ) {}
}
