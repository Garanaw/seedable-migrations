<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder as BaseSeeder;
use Illuminate\Support\Carbon;

abstract class Seeder extends BaseSeeder
{
    protected Carbon $now;

    /**
     * Create a new Seeder instance.
     *
     * @param DatabaseManager $db
     * @param Container $container
     * @return void
     */
    public function __construct(
        protected DatabaseManager $db,
        Container $container,
    ) {
        $this->now = Carbon::now();
        $this->setContainer($container);
    }

    /**
     * Run the database seeds.
     *
     * @return bool
     */
    abstract public function run(): bool;

    /**
     * Get the data to seed.
     *
     * @return array
     */
    abstract protected function getData(): array;
}
