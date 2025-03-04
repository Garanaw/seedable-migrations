<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations;

use Garanaw\SeedableMigrations\Contracts\SeedableMigration;
use Garanaw\SeedableMigrations\Enum\SeedAt;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Migrations\Migration as BaseMigration;
use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
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
     *
     * @param  class-string<T>  $blueprint
     */
    protected function setBlueprint(string $blueprint): void
    {
        $version = str(app()->version())->explode('.')->first();

        $callback = match ($version) {
            '12' => static fn (Connection $connection, string $table, \Closure $callback = null): BaseBlueprint => new $blueprint($connection, $table, $callback),
            default => static fn (string $table, \Closure $callback = null): BaseBlueprint => new $blueprint($table, $callback),
        };

        $this->schema->blueprintResolver($callback);
    }

    /**
     * Checks if there are seeders to run after the migration is up.
     */
    public function hasUpSeeders(): bool
    {
        return $this->upSeeders()->isNotEmpty();
    }

    /**
     * Checks if there are seeders to run after the migration is down.
     */
    public function hasDownSeeders(): bool
    {
        return $this->downSeeders()->isNotEmpty();
    }

    /**
     * Checks if there are seeders to run after the migration is up or down.
     */
    public function hasSeeders(string $method): bool
    {
        return $this->getSeeders($method)->isNotEmpty();
    }

    /**
     * Gets the seeders to run after the migration is up or down.
     *
     * @template T of SeedableMigration
     *
     * @return Collection<int, class-string<T>>
     */
    public function getSeeders(string $method): Collection
    {
        $callable = "{$method}Seeders";

        return Collection::wrap($this->$callable() ?? []);
    }

    /**
     * Determines whether the migration should seed.
     */
    public function shouldSeed(): bool
    {
        return true;
    }

    /**
     * Returns seeders to run on the UP method
     *
     * @return Collection<int, class-string<Seeder>>
     */
    public function upSeeders(): Collection
    {
        return new Collection();
    }

    /**
     * Returns seeders to run on the DOWN method
     *
     * @return Collection<int, class-string<Seeder>>
     */
    public function downSeeders(): Collection
    {
        return new Collection();
    }

    public function seedAt(): SeedAt
    {
        return SeedAt::NEVER;
    }
}
