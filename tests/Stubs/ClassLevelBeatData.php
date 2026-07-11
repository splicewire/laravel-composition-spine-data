<?php

namespace Rushing\CompositionSpineData\Tests\Stubs;

use Rushing\CompositionSpineData\Attributes\Beat;
use Rushing\CompositionSpineData\Attributes\BeatKind;
use Rushing\CompositionSpineData\Attributes\Cache;
use Rushing\CompositionSpineData\Attributes\Generate;
use Rushing\CompositionSpineData\Attributes\Ground;
use Rushing\CompositionSpineData\Attributes\MaxDepth;
use Rushing\CompositionSpineData\Attributes\Pause;
use Rushing\CompositionSpineData\Attributes\Polish;
use Rushing\CompositionSpineData\Attributes\Prose;
use Rushing\CompositionSpineData\Attributes\ProseRole;
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
