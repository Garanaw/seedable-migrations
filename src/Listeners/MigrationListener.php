<?php

declare(strict_types=1);

namespace Garanaw\SeedableMigrations\Listeners;

use Garanaw\SeedableMigrations\Contracts\SeedableMigration;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;

/**
 * @template T of SeedableMigration
 */
abstract class MigrationListener
{
    /** @var array<int, class-string<T>> */
    protected array $failedSeeders = [];

    public function __construct(
        protected Container $container,
    ) {
    }

    /**
     * Handles the migration.
     *
     * @param SeedableMigration $migration
     * @param string $method
     * @return void
     */
    protected function handleMigration(SeedableMigration $migration, string $method): void
    {
        if (! $this->shouldSeedNow($migration)) {
            return;
        }

        $this->seed(migration: $migration, method: $method);
    }

    /**
     * Runs the seeders.
     *
     * @param class-string<T> $seederName
     * @return void
     * @throws BindingResolutionException
     */
    protected function runSeeder(string $seederName): void
    {
        $seeder = $this->container->make($seederName);

        $result = $this->container->call([$seeder, 'run']);

        if ($result === false) {
            $this->failedSeeders[] = $seederName;
        }
    }

    /**
     * Gets the failed seeders.
     *
     * @param SeedableMigration $migration
     * @param string $method
     * @return void
     */
    protected function seed(SeedableMigration $migration, string $method): void
    {
        $callable = [$this, "{$method}Seed"];

        if (is_callable($callable)) {
            $this->container->call($callable, ['migration' => $migration]);
        }
    }

    /**
     * Runs the seeders after the migration is up.
     *
     * @param SeedableMigration $migration
     * @return void
     * @throws BindingResolutionException
     */
    protected function upSeed(SeedableMigration $migration): void
    {
        if (! $migration->hasUpSeeders()) {
            return;
        }

        $migration->upSeeders()->each(fn (string $name) => $this->runSeeder($name));
    }

    /**
     * Runs the seeders after the migration is down.
     *
     * @param SeedableMigration $migration
     * @return void
     * @throws BindingResolutionException
     */
    protected function downSeed(SeedableMigration $migration): void
    {
        if (! $migration->hasDownSeeders()) {
            return;
        }

        $migration->downSeeders()->each(fn (string $name) => $this->runSeeder($name));
    }

    /**
     * Determines whether the migration should seed.
     *
     * @param SeedableMigration $migration
     * @return bool
     */
    abstract protected function shouldSeedNow(SeedableMigration $migration): bool;
}
