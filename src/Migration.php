<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations;

use Garanaw\SeedableMigrations\Contracts\SeedableMigration;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Migrations\Migration as BaseMigration;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Collection;

abstract class Migration extends BaseMigration implements SeedableMigration
{
    protected Connection $db;

    protected Builder $schema;

    public function __construct()
    {
        /** @var DatabaseManager $manager */
        $manager = app(DatabaseManager::class);
        $this->db = $manager->connection();
        $this->schema = $manager->getSchemaBuilder();
        $this->setBlueprint(Blueprint::class);
    }

    /**
     * Gets the seeders to run after the migration is up.
     *
     * @template T of BaseBlueprint
     * @param class-string<T> $blueprint
     * @return void
     */
    protected function setBlueprint(string $blueprint): void
    {
        $this->schema->blueprintResolver(
            static fn (string $table, ?\Closure $callback = null): BaseBlueprint => new $blueprint($table, $callback)
        );
    }

    /**
     * Checks if there are seeders to run after the migration is up.
     *
     * @return bool
     */
    public function hasUpSeeders(): bool
    {
        return $this->upSeeders()->isNotEmpty();
    }

    /**
     * Checks if there are seeders to run after the migration is down.
     *
     * @return bool
     */
    public function hasDownSeeders(): bool
    {
        return $this->downSeeders()->isNotEmpty();
    }

    /**
     * Checks if there are seeders to run after the migration is up or down.
     *
     * @param string $method
     * @return bool
     */
    public function hasSeeders(string $method): bool
    {
        return $this->getSeeders($method)->isNotEmpty();
    }

    /**
     * Gets the seeders to run after the migration is up or down.
     *
     * @template T of SeedableMigration
     * @param string $method
     * @return Collection<int, class-string<T>>
     */
    public function getSeeders(string $method): Collection
    {
        $callable = "{$method}Seeders";
        return Collection::wrap($this->$callable() ?? []);
    }

    /**
     * Determines whether the migration should seed.
     *
     * @return bool
     */
    public function shouldSeed(): bool
    {
        return true;
    }
}
