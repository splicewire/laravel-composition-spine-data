<?php

namespace Rushing\CompositionSpineData\Vocabulary;

/**
 * How a generation keyword's value domain is *reflected* from its owner — never a
 * hand-authored value schema. {@see GrammarVocabulary} resolves each of these into a
 * JSON-Schema fragment + a TypeScript type by reflecting the referenced enum / method /
 * constructor, so adding an enum case (or a ctor param) flows into the described
 * vocabulary with no describer edit.
 */
enum ValueSource
{
    /** A backed enum: value domain is its live `::cases()` (e.g. BeatKind, ProseRole). */
    case Enum;

    /** A method's declared return type, projected as a union (e.g. `Ground::keyword(): string|bool`). */
    case Union;

    /** A plain boolean flag (e.g. Pause::enabled, Polish::auto). */
    case Boolean;

    /** A plain integer (the read-side max-depth cap). */
    case Integer;

    /** A plain string (a note, a dispatch handle). */
    case Text;

    /** An object whose shape is reflected from a class constructor (e.g. Cache). */
    case Object_;
}
