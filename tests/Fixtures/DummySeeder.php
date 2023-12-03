<?php

declare(strict_types=1);

namespace Garanaw\SeedableMigrations\Tests\Fixtures;

use Garanaw\SeedableMigrations\Seeder;

class DummySeeder extends Seeder
{

    public function run(): bool
    {
        return true;
    }

    protected function getData(): array
    {
        return [];
    }
}