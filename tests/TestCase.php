<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        // A cached config ignores phpunit.xml, so RefreshDatabase would wipe the
        // real database (it emptied blv2controller_db once). Checked here, before
        // RefreshDatabase runs, so nothing is touched.
        if ($app->configurationIsCached() || $app['config']->get('database.default') !== 'sqlite') {
            throw new \RuntimeException('Tests must run on the in-memory SQLite database. Run "php artisan config:clear" and try again.');
        }

        return $app;
    }
}
