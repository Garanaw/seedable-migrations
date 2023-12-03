# <p align="center">Laravel Seedable Migrations</p>

<p align="center">
<a href="https://github.com/Garanaw/seedable-migrations/actions"><img src="https://github.com/Garanaw/seedable-migrations/actions/workflows/tests.yml/badge.svg" alt="Tests"></a>
<a href="https://github.com/Garanaw/seedable-migrations/actions"><img src="https://github.com/Garanaw/seedable-migrations/actions/workflows/pint.yml/badge.svg" alt="Linting"></a>
</p>

## About

Laravel provides a migration system with seeding. However, the seeding system is not very flexible. This package provides a more flexible way to seed your database with seed files linked to migrations.

You can run your seeding after each migration, or at the end of the batch. You can also specify seeder files that should run on the UP method or the DOWN method of your migrations.

## Install

To install the package, run the following command:

```bash
composer require garanaw/seedable-migrations
```

After installing the package, you need to publish the migration stubs:

```bash
php artisan vendor:publish --provider="Garanaw\SeedableMigrations\SeedableMigrationsServiceProvider"
```

If you don't want to publish the stubs, you will need to extend your migrations manually from the `Garanaw\SeedableMigrations\Migration` class. 

## The new migration file

The migration files will look a bit different now when you run the command `php artisan make:migration`. The new migration files will look like this:

```php
<?php

declare(strict_types=1);

use Garanaw\SeedableMigrations\Blueprint;
use Garanaw\SeedableMigrations\Enum\SeedAt;
use Garanaw\SeedableMigrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $this->schema->create(table: $this->getTable(), callback: static function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function seedAt(): SeedAt
    {
        return SeedAt::EACH;
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->getTable());
    }
}
```

A few things to note here:

- The schema now is a property of the migration class. If you prefer the facade, you can still use it with `Schema::create()`.
- The `up()` and `down()` now have a return typehint of `void`. This is not mandatory, but it is recommended.
- Two new methods are added: `getTable()` and `seedAt()`. The `getTable()` method is used to get the table name of the migration.
- The `seedAt()` method is used to specify when the seeder should run. You can return `SeedAt::EACH` to run the seeder after each migration, or `SeedAt::BATCH` to run the seeder at the end of the batch. If you don't want to run the seeder, you can return `SeedAt::NONE`.

## The new seeder file

The seeder files will look a bit different now when you run the command `php artisan make:seeder`. The new seeder files will look like this:

```php
<?php

declare(strict_types=1);

use Garanaw\SeedableMigrations\Seeder;

return MySeeder extends Seeder
{
    public function run(): bool
    {
        // Your seeder code here
    }

    public function getData(): array
    {
        return [];
    }
}
```

A few things to note here:

- The `run()` method is now mandatory. This is where you should put your seeder code.
- The `getData()` method is used to return the data that should be seeded. This method is mandatory now as it follows an interface. If you don't want to seed anything, you can return an empty array.
- The `run()` method now has a return typehint of `bool`. This is mandatory, as it follows an interface, and it's used to store the failed seeders.
- If your seeder contains a method called `configure()`, it will be called through the `Container::call()`. This means that you can use dependency injection in your seeder class to add extra configuration if needed.
- Alternatively, you can create a `__construct()` method in your seeder class. This method will be called before the `run()` method, and you can use dependency injection in it, but it will require the parameters from the parent class.

## How to use

To run your migrations, you can use the `php artisan migrate` command as usual. The seeding will be done automatically.

To specify the seeder files associated to a migration, you will need to add some methods to the migration file:

```php
    public function seedersUp(): \Illuminate\Support\Collection
    {
        return collect([
            MySeeder::class,
        ]);
    }

    public function seedersDown(): \Illuminate\Support\Collection
    {
        return collect([
            MySeeder::class,
        ]);
    }
```

The `seedersUp()` method is used to specify the seeder files that should run on the UP method of the migration. The `seedersDown()` method is used to specify the seeder files that should run on the DOWN method of the migration. None of them is mandatory. If you don't specify any seeder file, the seeder will not run.

Another optional method is the `shouldSeed()` method. This method is used to specify if the seeder should run or not. This method is useful if you want to run the seeder only on specific environments. The default implementation is:

```php
    public function shouldSeed(): bool
    {
        return true;
    }
```

Within the `shouldSeed()` method, you can use the `app()->environment()` method to check the environment, or any other logic to determine if the seeder should run or not.

To configure your seeder, you only need to add a `configure()` method to your seeder class:

```php
    public function configure(
        \Illuminate\Config\Repository $config,
        \Illuminate\Foundation\Application $app,
        \Illuminate\Contracts\Console\Kernel $artisan,
        \Illuminate\Contracts\Debug\ExceptionHandler $exceptionHandler,
        App\Providers\RouteServiceProvider $routeServiceProvider,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        // Your configuration code here. You can inject anything that is configured in your container
    }
```

## Security Vulnerabilities

If you discover a security vulnerability, please create an issue using the issue tracker.

## License

The Seedable Migrations library is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
