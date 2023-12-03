<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations;

use Garanaw\SeedableMigrations\Listeners\MigrationEndedListener;
use Garanaw\SeedableMigrations\Listeners\MigrationsEndedListener;
use Illuminate\Database\Events\MigrationEnded;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class SeedableMigrationsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->about();
        $this->publishStubs();
        $this->registerListeners();
    }

    protected function about(): void
    {
        AboutCommand::add('Seedable Migrations', [
            'version' => '1.0.0',
        ]);
    }

    protected function registerListeners(): void
    {
        $this->app['events']->listen(MigrationEnded::class, MigrationEndedListener::class);
        $this->app['events']->listen(MigrationsEnded::class, MigrationsEndedListener::class);
    }

    protected function publishStubs(): void
    {
        if($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/stubs/migration.stub' => base_path('stubs/migration.stub'),
                __DIR__ . '/stubs/migration.create.stub' => base_path('stubs/migration.create.stub'),
                __DIR__ . '/stubs/migration.update.stub' => base_path('stubs/migration.update.stub'),
                __DIR__ . '/stubs/seeder.stub' => base_path('stubs/seeder.stub'),
            ], 'stubs');
        }
    }
}
