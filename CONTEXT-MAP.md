# laravel-composition-spine-data — Context Map (spoke)

The satellite-facing DTO/schema half of the composition engine (the `#[Beat]`/`#[Generate]`/
`#[Ground]` attributes, `BeatGrammar`, angle contracts, `StructuredExport`). Layering,
upstream, and shared seam rules: `splicewire-app/CONTEXT-MAP.md`.

## The seam this file exists to record

Its consumer lives in a **different, in-app** repo and isn't visible from here:
`laravel-composition-engine` (`Rushing\CompositionEngine\`, private) depends on this
package — **never the reverse** (1:1 parity). Why this package exists and what stays in the
engine: app ADR-0044.

## Grounding contracts

Two host-implemented seams keep the grounding vocabulary in the spine while its concrete
resolution stays per-vertical:

- **`GroundingCategoryContract`** — the stable string identity of a grounding category
  (characters, locations, watch, …) so the engine can key providers without enumerating one
  app's taxonomy.
- **`GroundingTokenResolver`** — `resolve(string $token): ?object`, the prose/fact trust
  boundary. A model may *cite* a `groundingToken` but the host resolves it back to a real
  record; a `null` return **drops** the token. It sinks here (not into any one consumer) so
  every projection — editorial Posts, and any future consumer — inherits safe-by-construction
  rehydration. Publishing's editorial `StructuredExport`→ProseMirror projector is the first
  consumer (publishing ADR-0002).
