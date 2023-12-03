<?php

use Garanaw\SeedableMigrations\Tests\Fixtures\MigrationWithDownSeeders;
use Garanaw\SeedableMigrations\Tests\Fixtures\MigrationWithUpSeeders;
use Garanaw\SeedableMigrations\Tests\Fixtures\NeverMigration;
use Illuminate\Support\Collection;

describe('Migration', function () {
    it('should be able to run up seeders', function () {
        $migration = new MigrationWithUpSeeders();
        $migration->up();
        expect($migration->hasUpSeeders())->toBeTrue()
            ->and($migration->hasDownSeeders())->toBeFalse()
            ->and($migration->hasSeeders('up'))->toBeTrue()
            ->and($migration->hasSeeders('down'))->toBeFalse()
            ->and($migration->shouldSeed())->toBeTrue()
            ->and($migration->getSeeders('up'))->toBeInstanceOf(Collection::class)
            ->and($migration->getSeeders('down'))->toBeInstanceOf(Collection::class)
            ->and($migration->getSeeders('up'))->toHaveCount(1)
            ->and($migration->getSeeders('down'))->toHaveCount(0);
    });

    it('should be able to run down seeders', function () {
        $migration = new MigrationWithDownSeeders();
        $migration->down();
        expect($migration->hasDownSeeders())->toBeTrue()
            ->and($migration->hasUpSeeders())->toBeFalse()
            ->and($migration->hasSeeders('up'))->toBeFalse()
            ->and($migration->hasSeeders('down'))->toBeTrue()
            ->and($migration->shouldSeed())->toBeTrue()
            ->and($migration->getSeeders('up'))->toBeInstanceOf(Collection::class)
            ->and($migration->getSeeders('down'))->toBeInstanceOf(Collection::class)
            ->and($migration->getSeeders('up'))->toHaveCount(0)
            ->and($migration->getSeeders('down'))->toHaveCount(1);
    });

    it('should not run up seeders', function () {
        $migration = new NeverMigration();
        $migration->up();
        expect($migration->hasUpSeeders())->toBeFalse()
            ->and($migration->hasDownSeeders())->toBeFalse()
            ->and($migration->hasSeeders('up'))->toBeFalse()
            ->and($migration->hasSeeders('down'))->toBeFalse()
            ->and($migration->shouldSeed())->toBeFalse()
            ->and($migration->getSeeders('up'))->toBeInstanceOf(Collection::class)
            ->and($migration->getSeeders('down'))->toBeInstanceOf(Collection::class)
            ->and($migration->getSeeders('up'))->toHaveCount(0)
            ->and($migration->getSeeders('down'))->toHaveCount(0);
    });

    it('should not run down seeders', function () {
        $migration = new NeverMigration();
        $migration->down();
        expect($migration->hasDownSeeders())->toBeFalse()
            ->and($migration->hasUpSeeders())->toBeFalse()
            ->and($migration->hasSeeders('up'))->toBeFalse()
            ->and($migration->hasSeeders('down'))->toBeFalse()
            ->and($migration->shouldSeed())->toBeFalse()
            ->and($migration->getSeeders('up'))->toBeInstanceOf(Collection::class)
            ->and($migration->getSeeders('down'))->toBeInstanceOf(Collection::class)
            ->and($migration->getSeeders('up'))->toHaveCount(0)
            ->and($migration->getSeeders('down'))->toHaveCount(0);
    });

    it('should set blueprint', function () {
        ['db' => $db] = mockDatabaseManager();
        new NeverMigration();
        $db->getSchemaBuilder()->shouldHaveReceived('blueprintResolver')->once();
    });
});
