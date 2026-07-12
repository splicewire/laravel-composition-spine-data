<?php

namespace Splicewire\CompositionSpineData\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Splicewire\CompositionSpineData\CompositionSpineDataServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
            CompositionSpineDataServiceProvider::class,
        ];
    }
}
