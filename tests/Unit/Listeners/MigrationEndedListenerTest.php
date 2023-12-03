<?php

declare(strict_types = 1);

use Garanaw\SeedableMigrations\Listeners\MigrationEndedListener;
use Garanaw\SeedableMigrations\Tests\Fixtures\ConfigurableSeeder;
use Garanaw\SeedableMigrations\Tests\Fixtures\SeedEachMigration;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Events\MigrationEnded;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

describe('Migration Ended Listener', function () {
    beforeEach(function () {
        Event::fake();
        mockDatabaseManager();
    });

    it('should run the migration seeders', function () {
        $migration = new SeedEachMigration();
        event(new MigrationEnded($migration, 'up'));

        Event::assertListening(MigrationEnded::class, MigrationEndedListener::class);
    });

    it('configures the seeder', function () {
        /** @var ConfigurableSeeder $seeder */
        $seeder = new class (mockDatabaseManager()['db'], app(Container::class)) extends ConfigurableSeeder {
            public function configure(Repository $config): void
            {
                expect($config)->toBeInstanceOf(Repository::class);
                parent::configure($config);
            }
        };
        $this->swap(ConfigurableSeeder::class, $seeder);

        $migration = new class extends SeedEachMigration {
            public function upSeeders(): Collection
            {
                return collect([
                    ConfigurableSeeder::class,
                ]);
            }
        };

        $event = new MigrationEnded($migration, 'up');

        /** @var MigrationEndedListener $listener */
        $listener = app(MigrationEndedListener::class);
        $listener->handle($event);
        expect($seeder->getConfig())->toBeInstanceOf(Repository::class);
    })->only();
});
