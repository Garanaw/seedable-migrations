<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations\Contracts;

use Garanaw\SeedableMigrations\Enum\SeedAt;
use Illuminate\Support\Collection;

interface SeedableMigration
{
    /**
     * Checks if there are seeders to run after the migration is up.
     *
     * @return bool
     */
    public function hasUpSeeders(): bool;

    /**
     * Checks if there are seeders to run after the migration is down.
     *
     * @return bool
     */
    public function hasDownSeeders(): bool;

    /**
     * Gets the seeders to run after the migration is up.
     *
     * @template T of SeedableMigration
     * @return Collection<int, class-string<T>>
     */
    public function upSeeders(): Collection;

    /**
     * Gets the seeders to run after the migration is down.
     *
     * @template T of SeedableMigration
     * @return Collection<int, class-string<T>>
     */
    public function downSeeders(): Collection;

    /**
     * Checks if the migration should run seeders.
     *
     * @return bool
     */
    public function shouldSeed(): bool;

    /**
     * Checks whether the migration should run seeders after the migration has run ot at the end of the batch.
     *
     * @return SeedAt
     */
    public function seedAt(): SeedAt;

    /**
     * Runs the migration.
     *
     * @return void
     */
    public function up(): void;

    /**
     * Reverts the migration.
     *
     * @return void
     */
    public function down(): void;

    /**
     * Gets the table name.
     *
     * @return string
     */
    public function getTable(): string;
}
