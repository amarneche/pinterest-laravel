<?php

namespace Pinterest;

use Pinterest\Auth\OAuthService;
use Pinterest\Client\PinterestClient;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PinterestServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('pinterest')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(PinterestClient::class, function ($app): PinterestClient {
            $config = $app['config']['pinterest'];

            return new PinterestClient(
                baseUrl: $config['base_url'] ?? 'https://api.pinterest.com',
                apiVersion: $config['api_version'] ?? 'v5',
                accessToken: $config['access_token'] ?? '',
                timeout: $config['timeout'] ?? 30,
                retryTimes: $config['retry']['times'] ?? 3,
                retrySleep: $config['retry']['sleep'] ?? 100,
            );
        });

        $this->app->singleton(OAuthService::class, function ($app): OAuthService {
            $config = $app['config']['pinterest'];

            return new OAuthService(
                clientId: $config['client_id'] ?? '',
                clientSecret: $config['client_secret'] ?? '',
                redirectUri: $config['redirect_uri'] ?? '',
                oauthUrl: $config['oauth_url'] ?? 'https://www.pinterest.com/oauth/',
                baseUrl: $config['base_url'] ?? 'https://api.pinterest.com',
                apiVersion: $config['api_version'] ?? 'v5',
                scopes: $config['scopes'] ?? '',
            );
        });

        $this->app->singleton(Pinterest::class, function ($app): Pinterest {
            return new Pinterest(
                client: $app->make(PinterestClient::class),
                oauth: $app->make(OAuthService::class),
            );
        });
    }
}
