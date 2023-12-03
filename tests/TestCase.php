<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations\Tests;

use Garanaw\SeedableMigrations\SeedableMigrationsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SeedableMigrationsServiceProvider::class,
        ];
    }
}
