<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations\Tests\Fixtures;

use Garanaw\SeedableMigrations\Enum\SeedAt;
use Illuminate\Support\Collection;

class MigrationWithUpSeeders extends DummyMigration
{
    public function seedAt(): SeedAt
    {
        return SeedAt::END;
    }

    public function hasUpSeeders(): bool
    {
        return $this->upSeeders()->isNotEmpty();
    }

    public function upSeeders(): Collection
    {
        return collect([
            app(DummySeeder::class),
        ]);
    }
}
