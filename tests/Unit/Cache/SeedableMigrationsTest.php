<?php

use Garanaw\SeedableMigrations\Cache\SeedableMigrations;
use Garanaw\SeedableMigrations\Tests\Fixtures\DummyMigration;
use Garanaw\SeedableMigrations\Tests\Fixtures\MigrationWithDownSeeders;
use Garanaw\SeedableMigrations\Tests\Fixtures\MigrationWithUpSeeders;
use Garanaw\SeedableMigrations\Tests\Fixtures\NeverMigration;

describe('SeedableMigration Cache', function () {
    beforeEach(function () {
        SeedableMigrations::clear();
    });

    it('stores migrations', function () {
        $migration = new DummyMigration();
        SeedableMigrations::add($migration);

        expect(SeedableMigrations::get())
            ->toBeArray()
            ->toHaveCount(1);
    });

    it('gets migrations', function () {
        $migration = new DummyMigration();
        SeedableMigrations::add($migration);

        foreach (SeedableMigrations::get() as $migration) {
            expect($migration)->toBeInstanceOf(DummyMigration::class);
        }
    });

    it('clears migrations', function () {
        $migration = new DummyMigration();
        SeedableMigrations::add($migration);

        SeedableMigrations::clear();

        expect(SeedableMigrations::get())
            ->toBeArray()
            ->toHaveCount(0);
    });

    it('removes a migration', function () {
        $migration = new DummyMigration();
        $migration2 = new class extends DummyMigration {
        };
        SeedableMigrations::add($migration);
        SeedableMigrations::add($migration2);

        SeedableMigrations::remove($migration);

        expect(SeedableMigrations::get())
            ->toHaveCount(1)
            ->and(SeedableMigrations::get())->not->toContain($migration)
            ->and(SeedableMigrations::get())->toContain($migration2);
    });

    it('adds many migrations', function () {
        $migration = new DummyMigration();
        $migration2 = new class extends DummyMigration {
        };
        SeedableMigrations::addMany([$migration, $migration2]);

        expect(SeedableMigrations::get())
            ->toHaveCount(2)
            ->and(SeedableMigrations::get())->toContain($migration)
            ->and(SeedableMigrations::get())->toContain($migration2);
    });

    it('returns migrations with seeders for method up', function () {
        $migration = new DummyMigration();
        $migration2 = new MigrationWithUpSeeders();
        SeedableMigrations::addMany([$migration, $migration2]);

        expect(SeedableMigrations::getUpSeedableMigrations())
            ->toHaveCount(1)
            ->and(SeedableMigrations::getUpSeedableMigrations())->toContain($migration2)
            ->and(SeedableMigrations::getUpSeedableMigrations())->not->toContain($migration);
    });

    it('returns migrations with seeders for method down', function () {
        $migration = new DummyMigration();
        $migration2 = new MigrationWithDownSeeders();
        SeedableMigrations::addMany([$migration, $migration2]);

        expect(SeedableMigrations::getDownSeedableMigrations())
            ->toHaveCount(1)
            ->and(SeedableMigrations::getDownSeedableMigrations())->toContain($migration2)
            ->and(SeedableMigrations::getDownSeedableMigrations())->not->toContain($migration);
    });

    it('does not return migrations with seeders that should seed never', function () {
        $migration = new NeverMigration();
        SeedableMigrations::add($migration);

        expect(SeedableMigrations::getUpSeedableMigrations())
            ->toHaveCount(0)
            ->and(SeedableMigrations::get())->toHaveCount(1)
            ->and(SeedableMigrations::getUpSeedableMigrations())->toHaveCount(0)
            ->and(SeedableMigrations::getDownSeedableMigrations())->toHaveCount(0);
    });
});
