<?php

declare(strict_types=1);

namespace Garanaw\SeedableMigrations\Listeners;

use Garanaw\SeedableMigrations\Cache\SeedableMigrations;
use Garanaw\SeedableMigrations\Contracts\SeedableMigration;
use Illuminate\Database\Events\MigrationsEnded;

class MigrationsEndedListener extends MigrationListener
{
    /**
     * Handles the migrations after the batch has finished migrating.
     *
     * @param MigrationsEnded $event
     * @return void
     */
    public function handle(MigrationsEnded $event): void
    {
        $method = sprintf(
            'get%sSeedableMigrations',
            ucfirst($event->method),
        );

        if (!method_exists(SeedableMigrations::class, $method)) {
            return;
        }

        SeedableMigrations::$method()
            ->filter(fn (SeedableMigration $migration) => $this->shouldSeedNow($migration))
            ->each(fn (SeedableMigration $migration) => $this->handleMigration($migration, $event->method));
    }

    /**
     * Checks if the migration should be seeded now.
     *
     * @param SeedableMigration $migration
     * @return bool
     */
    protected function shouldSeedNow(SeedableMigration $migration): bool
    {
        return $migration->shouldSeed() && $migration->seedAt()->end();
    }
}
