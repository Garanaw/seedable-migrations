<?php

declare(strict_types=1);

namespace Garanaw\SeedableMigrations\Tests\Fixtures;

use Garanaw\SeedableMigrations\Enum\SeedAt;
use Illuminate\Support\Collection;

class SeedEachMigration extends DummyMigration
{
    public function seedAt(): SeedAt
    {
        return SeedAt::EACH;
    }

    public function shouldSeed(): bool
    {
        return true;
    }

    public function upSeeders(): Collection
    {
        return collect([
            app(DummySeeder::class),
        ]);
    }

    public function downSeeders(): Collection
    {
        return collect([
            app(DummySeeder::class),
        ]);
    }
}