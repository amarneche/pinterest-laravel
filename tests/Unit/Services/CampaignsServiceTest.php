<?php

use Illuminate\Support\Facades\Http;
use Pinterest\Client\PinterestClient;
use Pinterest\Services\CampaignsService;
use Pinterest\Support\ApiResponse;
use Pinterest\Support\PaginatedResponse;

beforeEach(function () {
    $this->client = new PinterestClient(
        baseUrl: 'https://api.pinterest.com',
        apiVersion: 'v5',
        accessToken: 'test-token',
        timeout: 30,
        retryTimes: 1,
        retrySleep: 0,
    );
    $this->service = new CampaignsService($this->client);
});

test('it lists campaigns for an ad account', function () {
    Http::fake([
        'api.pinterest.com/v5/ad_accounts/acc123/campaigns*' => Http::response([
            'items' => [
                ['id' => 'camp1', 'name' => 'Campaign 1', 'status' => 'ACTIVE'],
                ['id' => 'camp2', 'name' => 'Campaign 2', 'status' => 'PAUSED'],
            ],
            'bookmark' => null,
        ], 200),
    ]);

    $response = $this->service->list('acc123');

    expect($response)
        ->toBeInstanceOf(PaginatedResponse::class)
        ->and($response->items())->toHaveCount(2)
        ->and($response->hasMorePages())->toBeFalse();
});

test('it gets a specific campaign', function () {
    Http::fake([
        'api.pinterest.com/v5/ad_accounts/acc123/campaigns/camp1' => Http::response([
            'id' => 'camp1',
            'name' => 'My Campaign',
            'status' => 'ACTIVE',
            'objective_type' => 'AWARENESS',
        ], 200),
    ]);

    $response = $this->service->get('acc123', 'camp1');

    expect($response)
        ->toBeInstanceOf(ApiResponse::class)
        ->and($response->get('name'))->toBe('My Campaign')
        ->and($response->get('objective_type'))->toBe('AWARENESS');
});

test('it creates a campaign', function () {
    Http::fake([
        'api.pinterest.com/v5/ad_accounts/acc123/campaigns' => Http::response([
            'items' => [
                ['data' => ['id' => 'new-camp', 'name' => 'New Campaign']],
            ],
        ], 200),
    ]);

    $response = $this->service->create('acc123', [
        [
            'name' => 'New Campaign',
            'objective_type' => 'AWARENESS',
            'status' => 'ACTIVE',
            'daily_spend_cap' => 1000000,
        ],
    ]);

    expect($response->successful())->toBeTrue();

    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && str_contains($request->url(), 'ad_accounts/acc123/campaigns');
    });
});

test('it updates a campaign', function () {
    Http::fake([
        'api.pinterest.com/v5/ad_accounts/acc123/campaigns' => Http::response([
            'items' => [
                ['data' => ['id' => 'camp1', 'status' => 'PAUSED']],
            ],
        ], 200),
    ]);

    $response = $this->service->update('acc123', [
        [
            'id' => 'camp1',
            'status' => 'PAUSED',
        ],
    ]);

    expect($response->successful())->toBeTrue();
});

test('it gets campaign analytics', function () {
    Http::fake([
        'api.pinterest.com/v5/ad_accounts/acc123/campaigns/analytics*' => Http::response([
            ['campaign_id' => 'camp1', 'impressions' => 10000],
        ], 200),
    ]);

    $response = $this->service->getAnalytics('acc123', [
        'campaign_ids' => 'camp1',
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-31',
        'columns' => 'IMPRESSION',
        'granularity' => 'TOTAL',
    ]);

    expect($response->successful())->toBeTrue();
});
