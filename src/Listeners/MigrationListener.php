<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations\Listeners;

use Garanaw\SeedableMigrations\Contracts\SeedableMigration;
use Garanaw\SeedableMigrations\Seeder;
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
        if (!$this->shouldSeedNow($migration)) {
            return;
        }

        $this->seed(migration: $migration, method: $method);
    }

    /**
     * Runs the seeders.
     *
     * @param Seeder|string $seeder
     * @return void
     * @throws BindingResolutionException
     */
    protected function runSeeder(Seeder|string $seeder): void
    {
        if (is_string($seeder)) {
            $seeder = $this->container->make($seeder);
        }

        $seeder = $this->configureIfRequired($seeder);

        $result = $this->container->call([$seeder, 'run']);

        if ($result === false) {
            $this->failedSeeders[] = $seeder;
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

    protected function configureIfRequired(Seeder $seeder): Seeder
    {
        if (!method_exists($seeder, 'configure')) {
            return $seeder;
        }

        $reflector = new \ReflectionClass($seeder);

        if ($reflector->getMethod('configure')->isPublic()) {
            $this->container->call([$seeder, 'configure']);
        }

        return $seeder;
    }

    /**
     * Runs the seeders after the migration is up.
     *
     * @param SeedableMigration $migration
     * @return void
     * @throws BindingResolutionException
     */
    public function upSeed(SeedableMigration $migration): void
    {
        if (!$migration->hasUpSeeders()) {
            return;
        }

        $migration->upSeeders()->each(fn (Seeder|string $seeder) => $this->runSeeder($seeder));
    }

    /**
     * Runs the seeders after the migration is down.
     *
     * @param SeedableMigration $migration
     * @return void
     * @throws BindingResolutionException
     */
    public function downSeed(SeedableMigration $migration): void
    {
        if (!$migration->hasDownSeeders()) {
            return;
        }

        $migration->downSeeders()->each(fn (Seeder $seeder) => $this->runSeeder($seeder));
    }

    /**
     * Determines whether the migration should seed.
     *
     * @param SeedableMigration $migration
     * @return bool
     */
    abstract protected function shouldSeedNow(SeedableMigration $migration): bool;
}
