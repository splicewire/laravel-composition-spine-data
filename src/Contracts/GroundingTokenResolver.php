<?php

namespace Rushing\CompositionSpineData\Contracts;

/**
 * The grounding trust boundary as a contract. A generation model may write *prose* but must
 * never emit a resolved *fact*: it can only *cite* a grounding token (using the existing
 * `groundingTokens` vocabulary — e.g. "asin:B01", "person:42", "provider:netflix"), and the
 * HOST resolves that token back to a real record.
 *
 * A `null` return **means drop the token** — so any projection built on this contract is
 * safe by construction: an unverified citation is never rendered, only cited-and-resolved
 * subjects survive. The host implements it per vertical (thingsontv resolves its catalog;
 * a satellite with no grounded content never implements it). It sinks to the spine — beside
 * {@see GroundingCategoryContract} — so *every* projection, not just editorial Posts,
 * inherits the same guarantee.
 */
interface GroundingTokenResolver
{
    /**
     * Resolve a cited grounding token to its real subject, or `null` to drop it.
     */
    public function resolve(string $token): ?object;
}
