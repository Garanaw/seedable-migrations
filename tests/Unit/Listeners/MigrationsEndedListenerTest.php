<?php

declare(strict_types = 1);

use Garanaw\SeedableMigrations\Cache\SeedableMigrations;
use Garanaw\SeedableMigrations\Listeners\MigrationsEndedListener;
use Garanaw\SeedableMigrations\Tests\Fixtures\EndOfBatchMigration;
use Garanaw\SeedableMigrations\Tests\Fixtures\NeverMigration;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Event;

describe('Migration Ended Listener', function () {
    beforeEach(function () {
        mockDatabaseManager();
    });

    it('should not run the migration seeders', function () {
        Event::fake();
        $migration = new NeverMigration();
        SeedableMigrations::add($migration);
        event(new MigrationsEnded('up'));

        Event::assertListening(MigrationsEnded::class, MigrationsEndedListener::class);
    });

    it('should run the migration seeders and clear after finished', function () {
        $migration = new EndOfBatchMigration();
        SeedableMigrations::add($migration);
        event(new MigrationsEnded('up'));

        expect(SeedableMigrations::get())->toBeEmpty();
    });
});
