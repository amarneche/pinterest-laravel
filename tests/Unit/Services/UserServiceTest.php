<?php

use Illuminate\Support\Facades\Http;
use Pinterest\Client\PinterestClient;
use Pinterest\Services\UserService;
use Pinterest\Support\ApiResponse;

beforeEach(function () {
    $this->client = new PinterestClient(
        baseUrl: 'https://api.pinterest.com',
        apiVersion: 'v5',
        accessToken: 'test-token',
        timeout: 30,
        retryTimes: 1,
        retrySleep: 0,
    );
    $this->service = new UserService($this->client);
});

test('it gets user account information', function () {
    Http::fake([
        'api.pinterest.com/v5/user_account*' => Http::response([
            'username' => 'testuser',
            'account_type' => 'BUSINESS',
            'profile_image' => 'https://example.com/avatar.jpg',
            'website_url' => 'https://example.com',
        ], 200),
    ]);

    $response = $this->service->getAccount();

    expect($response)
        ->toBeInstanceOf(ApiResponse::class)
        ->and($response->get('username'))->toBe('testuser')
        ->and($response->get('account_type'))->toBe('BUSINESS');
});

test('it gets user analytics', function () {
    Http::fake([
        'api.pinterest.com/v5/user_account/analytics*' => Http::response([
            'all' => [
                'daily_metrics' => [
                    ['date' => '2024-01-01', 'impressions' => 1000],
                ],
            ],
        ], 200),
    ]);

    $response = $this->service->getAnalytics([
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-31',
        'metric_types' => 'IMPRESSION',
    ]);

    expect($response->successful())->toBeTrue();
});

test('it gets top pins analytics', function () {
    Http::fake([
        'api.pinterest.com/v5/user_account/analytics/top_pins*' => Http::response([
            'pins' => [
                ['id' => 'pin1', 'impressions' => 5000],
            ],
        ], 200),
    ]);

    $response = $this->service->getTopPinsAnalytics([
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-31',
        'sort_by' => 'IMPRESSION',
    ]);

    expect($response->successful())->toBeTrue();
});
