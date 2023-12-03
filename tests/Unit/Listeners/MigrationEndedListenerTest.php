<?php

declare(strict_types = 1);

use Garanaw\SeedableMigrations\Listeners\MigrationEndedListener;
use Garanaw\SeedableMigrations\Tests\Fixtures\SeedEachMigration;
use Illuminate\Database\Events\MigrationEnded;
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
});
