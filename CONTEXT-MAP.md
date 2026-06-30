# laravel-composition-spine-data — Context Map (spoke)

The satellite-facing DTO/schema half of the composition engine (the `#[Beat]`/`#[Generate]`/
`#[Ground]` attributes, `BeatGrammar`, angle contracts, `StructuredExport`). Layering,
upstream, and shared seam rules: `splicewire-app/CONTEXT-MAP.md`.

## The seam this file exists to record

Its consumer lives in a **different, in-app** repo and isn't visible from here:
`laravel-composition-engine` (`Rushing\CompositionEngine\`, private) depends on this
package — **never the reverse** (1:1 parity). Why this package exists and what stays in the
engine: app ADR-0044.
