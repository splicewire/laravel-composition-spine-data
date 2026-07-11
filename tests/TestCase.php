<?php

namespace Rushing\CompositionSpineData\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Rushing\CompositionSpineData\CompositionSpineDataServiceProvider;
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
