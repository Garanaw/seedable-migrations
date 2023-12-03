<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations\Cache;

use Garanaw\SeedableMigrations\Contracts\SeedableMigration;
use Illuminate\Support\Collection;

class SeedableMigrations
{
    /** @var array<int, SeedableMigration> */
    private static array $migrations = [];

    /**
     * Adds a migration to the list of seedable migrations to seed at the end of the batch.
     *
     * @param SeedableMigration $migration
     * @return void
     */
    public static function add(SeedableMigration $migration): void
    {
        self::$migrations[] = $migration;
    }

    /**
     * Adds many migrations to the list of seedable migrations to seed at the end of the batch.
     *
     * @param array<int, SeedableMigration> $migrations
     * @return void
     */
    public static function addMany(array $migrations): void
    {
        self::$migrations = array_merge(self::$migrations, $migrations);
    }

    /**
     * Removes a migration from the list of seedable migrations to seed at the end of the batch.
     *
     * @param SeedableMigration $migration
     * @return void
     */
    public static function remove(SeedableMigration $migration): void
    {
        $key = array_search($migration, self::$migrations, true);
        if ($key !== false) {
            unset(self::$migrations[$key]);
        }
    }

    /**
     * Gets the list of seedable migrations to seed at the end of the batch.
     *
     * @template T of SeedableMigration
     * @return array<int, class-string<T>>
     */
    public static function get(): array
    {
        return self::$migrations;
    }

    /**
     * Clears the list of seedable migrations to seed at the end of the batch.
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$migrations = [];
    }

    /**
     * Gets the list of seedable migrations to seed at the end of the batch on UP method.
     *
     * @template T of SeedableMigration
     * @return Collection<int, class-string<T>>
     */
    public static function getUpSeedableMigrations(): Collection
    {
        return collect(self::$migrations)
            ->filter(
                fn (SeedableMigration $migration) => $migration->shouldSeed()
                    && $migration->seedAt()->end()
                    && $migration->hasUpSeeders()
            );
    }

    /**
     * Gets the list of seedable migrations to seed at the end of the batch on DOWN method.
     *
     * @template T of SeedableMigration
     * @return Collection<int, class-string<T>>
     */
    public static function getDownSeedableMigrations(): Collection
    {
        return collect(self::$migrations)
            ->filter(
                fn (SeedableMigration $migration) => $migration->shouldSeed()
                    && $migration->seedAt()->end()
                    && $migration->hasDownSeeders()
            );
    }
}
