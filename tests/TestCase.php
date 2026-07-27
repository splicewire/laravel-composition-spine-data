<?php

namespace Splicewire\Composition\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Splicewire\Composition\CompositionDataServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
            CompositionDataServiceProvider::class,
        ];
    }
}
