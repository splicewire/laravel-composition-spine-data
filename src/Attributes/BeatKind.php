<?php

declare(strict_types=1);

namespace Rushing\CompositionSpineData\Attributes;

/**
 * How the interpreter treats a Beat when it reaches the frontier.
 *
 * - Expandable: the Beat is expanded by one focused StructuredGenerator call
 *   that realizes its children (an outline node, a section that yields cells).
 * - Writable: the Beat is a leaf the model writes directly (a heading, a
 *   paragraph's prose) — no further expansion.
 */
enum BeatKind: string
{
    case Expandable = 'expandable';
    case Writable = 'writable';
}
