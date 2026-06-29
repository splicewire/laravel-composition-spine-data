<?php

declare(strict_types=1);

namespace Rushing\CompositionSpineData\Contracts;

/**
 * An abstract component an angle encourages the body to use (e.g. "destination CTA", "where-to-watch",
 * "product showcase") — independent of how any profile renders it. The app supplies the concrete
 * vocabulary (a backed enum); each generative profile realizes these intents into its own concrete
 * embed/node prompt labels via `realizeComponents()`.
 */
interface ComponentIntentContract
{
    /**
     * The stable string identity of this component intent (e.g. "destination-cta", "where-to-watch").
     */
    public function value(): string;
}
