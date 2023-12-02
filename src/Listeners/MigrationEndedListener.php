<?php

declare(strict_types=1);

namespace Garanaw\SeedableMigrations\Listeners;

use Garanaw\SeedableMigrations\Cache\SeedableMigrations;
use Garanaw\SeedableMigrations\Contracts\SeedableMigration;
use Illuminate\Database\Events\MigrationEnded;

class MigrationEndedListener extends MigrationListener
{
    /**
     * Handles the migrations after it has finished migrating.
     *
     * @param MigrationEnded $event
     * @return void
     */
    public function handle(MigrationEnded $event): void
    {
        $migration = $event->migration;

        if (! $migration instanceof SeedableMigration) {
            return;
        }

        if (! $this->shouldSeedNow($migration)) {
            $this->saveIfShouldSeedLater($migration, $event);
            return;
        }

        $this->handleMigration($migration, $event->method);
    }

    /**
     * Checks if the migration should be seeded now.
     *
     * @param SeedableMigration $migration
     * @return bool
     */
    protected function shouldSeedNow(SeedableMigration $migration): bool
    {
        return $migration->shouldSeed() && $migration->seedAt()->each();
    }

    /**
     * Saves the migration to be seeded later.
     *
     * @param SeedableMigration $migration
     * @param MigrationEnded $event
     * @return void
     */
    protected function saveIfShouldSeedLater(SeedableMigration $migration, MigrationEnded $event): void
    {
        if (! $migration->shouldSeed()) {
            return;
        }

        if ($migration->seedAt()->end()) {
            SeedableMigrations::add($migration);
        }
    }
}
