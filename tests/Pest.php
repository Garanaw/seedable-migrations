<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Garanaw\SeedableMigrations\Tests\TestCase;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Schema\Builder;

uses(TestCase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function mockConnection(): mixed
{
    return tap(
        mock(Connection::class, function ($mock) {
            $mock->shouldReceive('getSchemaBuilder->getColumnListing')->andReturn([]);
        }),
        fn ($mock) => app()->instance(Connection::class, $mock)
    );
}

function mockSchema(): mixed
{
    return tap(
        mock(Builder::class, function ($mock) {
            $mock->shouldReceive('blueprintResolver')->andReturnUsing(function ($callback) {
                return $callback('table', null);
            });
        }),
        fn ($mock) => app()->instance(Builder::class, $mock)
    );
}

function mockDatabaseManager(): array
{
    $connection = mockConnection();
    $schema = mockSchema();

    $db = tap(
        mock(DatabaseManager::class, function ($mock) use ($connection, $schema) {
            $mock->shouldReceive('connection')->andReturn($connection);
            $mock->shouldReceive('getSchemaBuilder')->andReturn($schema);
        }),
        fn ($mock) => app()->instance(DatabaseManager::class, $mock)
    );

    return [
        'connection' => $connection,
        'schema' => $schema,
        'db' => $db,
    ];
}

//function something()
//{
//    // ..
//}
