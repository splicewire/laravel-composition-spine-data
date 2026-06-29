<?php

declare(strict_types=1);

namespace Rushing\CompositionSpineData\Attributes;

/**
 * How a grounding field should be treated in prose — the field-level prose
 * disposition that lets a grounding type self-describe, replacing a blanket
 * prose-neutrality rule.
 *
 * - Subject: the thing the prose is about (a place, a show, a person) — write
 *   about it freely.
 * - RenderOnly: a swappable provider/marketplace detail bound to the rendered
 *   embed (a booking URL, a shop link). The provider behind it must not be named
 *   or discussed in prose unless a brief asks; the embed carries the brand, the
 *   prose never does.
 * - Nameable: a value that is the point of naming (the streaming providers of a
 *   where-to-watch box) — it may be named in prose.
 */
enum ProseRole: string
{
    case Subject = 'subject';
    case RenderOnly = 'render-only';
    case Nameable = 'nameable';
}
