<?php

declare(strict_types=1);

namespace Garanaw\SeedableMigrations\Tests\Fixtures;

use Garanaw\SeedableMigrations\Enum\SeedAt;
use Garanaw\SeedableMigrations\Migration;
use Illuminate\Support\Collection;

class DummyMigration extends Migration
{
    public function upSeeders(): Collection
    {
        return collect([]);
    }

    public function downSeeders(): Collection
    {
        return collect([]);
    }

    public function seedAt(): SeedAt
    {
        return SeedAt::NEVER;
    }

    public function up(): void
    {

    }

    public function down(): void
    {

    }

    public function getTable(): string
    {
        return 'dummy';
    }
}