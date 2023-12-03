<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations\Tests\Fixtures;

use Illuminate\Config\Repository;

class ConfigurableSeeder extends DummySeeder
{
    private Repository $config;

    public function configure(Repository $config): void
    {
        $this->config = $config;
    }

    public function getConfig(): Repository
    {
        return $this->config;
    }
}
