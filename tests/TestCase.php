<?php

namespace Tests;

use App\Services\GradingService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        GradingService::flushCache();
    }

    /**
     * Keep tests isolated even if a developer's .env points at MySQL.
     */
    protected function getEnvironmentSetUp($app): void
    {
        if (env('APP_ENV') !== 'testing') {
            throw new \RuntimeException('Tests may only run with APP_ENV=testing.');
        }

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }
}
