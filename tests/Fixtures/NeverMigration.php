<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations\Tests\Fixtures;

use Garanaw\SeedableMigrations\Enum\SeedAt;

class NeverMigration extends DummyMigration
{
    public function seedAt(): SeedAt
    {
        return SeedAt::NEVER;
    }

    public function shouldSeed(): bool
    {
        return false;
    }
}
