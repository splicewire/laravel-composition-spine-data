<?php

declare(strict_types=1);

namespace Rushing\CompositionSpineData\Angles;

use Rushing\CompositionSpineData\Contracts\AngleContract;
use Rushing\CompositionSpineData\Contracts\AngleContributor;
use Rushing\CompositionSpineData\Contracts\ComponentIntentContract;
use Rushing\CompositionSpineData\Contracts\ConversionIntentContract;
use Rushing\CompositionSpineData\Contracts\GroundingCategoryContract;

/**
 * The merged result of composing an ordered set of {@see AngleContributor}s.
 * The whole promotion pipeline reads this — the grounding emphasis orders the grounding sections, the
 * section plan and prompt fragment shape the body prompt, and the primary intent sets the primary CTA —
 * so layering a second angle changes only what the composer merges, never the consumers.
 */
final readonly class ComposedShape
{
    /**
     * @param  array<int, AngleContract>  $angles  ordered, primary first
     * @param  array<int, string>  $sections  ordered body section plan
     * @param  array<int, GroundingCategoryContract>  $groundingEmphasis  grounding categories, most-emphasised first
     * @param  array<int, ComponentIntentContract>  $encouragedComponents  abstract component intents
     */
    public function __construct(
        public array $angles,
        public ?ConversionIntentContract $intent,
        public array $sections,
        public array $groundingEmphasis,
        public array $encouragedComponents,
        public string $promptFragment,
        public string $titleGuidance = '',
    ) {}

    public function primaryAngle(): AngleContract
    {
        return $this->angles[0];
    }

    public function hasAngle(AngleContract $angle): bool
    {
        return in_array($angle, $this->angles, true);
    }
}
