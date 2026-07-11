<?php

namespace Rushing\CompositionSpineData\Tests\Stubs;

use Rushing\CompositionSpineData\Contracts\ConversionIntentContract;

/**
 * A stand-in conversion intent that owns its own CTA wording, exercising the host-owned
 * {@see ConversionIntentContract::primaryCtaGuidance()} the composed shape carries.
 */
enum StubIntent: string implements ConversionIntentContract
{
    case Act = 'act';

    public function value(): string
    {
        return $this->value;
    }

    public function primaryCtaGuidance(): string
    {
        return 'do the thing';
    }
}
