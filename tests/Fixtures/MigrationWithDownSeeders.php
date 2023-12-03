<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations\Tests\Fixtures;

use Garanaw\SeedableMigrations\Enum\SeedAt;
use Illuminate\Support\Collection;

class MigrationWithDownSeeders extends DummyMigration
{
    public function seedAt(): SeedAt
    {
        return SeedAt::END;
    }

    public function hasDownSeeders(): bool
    {
        return $this->downSeeders()->isNotEmpty();
    }

    public function downSeeders(): Collection
    {
        return collect([
            app(DummySeeder::class),
        ]);
    }
}
