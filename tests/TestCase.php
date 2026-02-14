<?php

namespace Pinterest\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Pinterest\PinterestServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            PinterestServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Pinterest' => \Pinterest\Facades\Pinterest::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('pinterest.client_id', 'test-client-id');
        $app['config']->set('pinterest.client_secret', 'test-client-secret');
        $app['config']->set('pinterest.redirect_uri', 'https://example.com/callback');
        $app['config']->set('pinterest.access_token', 'test-access-token');
        $app['config']->set('pinterest.refresh_token', 'test-refresh-token');
        $app['config']->set('pinterest.base_url', 'https://api.pinterest.com');
        $app['config']->set('pinterest.api_version', 'v5');
        $app['config']->set('pinterest.oauth_url', 'https://www.pinterest.com/oauth/');
        $app['config']->set('pinterest.scopes', 'boards:read,pins:read,user_accounts:read');
        $app['config']->set('pinterest.timeout', 30);
        $app['config']->set('pinterest.retry.times', 1);
        $app['config']->set('pinterest.retry.sleep', 0);
    }
}
