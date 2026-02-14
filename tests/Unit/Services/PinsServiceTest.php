<?php

use Illuminate\Support\Facades\Http;
use Pinterest\Client\PinterestClient;
use Pinterest\Services\PinsService;
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
    $this->service = new PinsService($this->client);
});

test('it lists pins with pagination', function () {
    Http::fake([
        'api.pinterest.com/v5/pins*' => Http::response([
            'items' => [
                ['id' => 'pin1', 'title' => 'Test Pin'],
            ],
            'bookmark' => 'page2',
        ], 200),
    ]);

    $response = $this->service->list(['page_size' => 10]);

    expect($response)
        ->toBeInstanceOf(PaginatedResponse::class)
        ->and($response->items())->toHaveCount(1)
        ->and($response->hasMorePages())->toBeTrue();
});

test('it gets a specific pin', function () {
    Http::fake([
        'api.pinterest.com/v5/pins/pin123' => Http::response([
            'id' => 'pin123',
            'title' => 'My Pin',
            'description' => 'A test pin',
            'link' => 'https://example.com',
        ], 200),
    ]);

    $response = $this->service->get('pin123');

    expect($response)
        ->toBeInstanceOf(ApiResponse::class)
        ->and($response->get('title'))->toBe('My Pin')
        ->and($response->get('link'))->toBe('https://example.com');
});

test('it creates a pin', function () {
    Http::fake([
        'api.pinterest.com/v5/pins' => Http::response([
            'id' => 'new-pin',
            'title' => 'New Pin',
            'board_id' => 'board123',
        ], 201),
    ]);

    $response = $this->service->create([
        'board_id' => 'board123',
        'title' => 'New Pin',
        'description' => 'A new pin',
        'media_source' => [
            'source_type' => 'image_url',
            'url' => 'https://example.com/image.jpg',
        ],
    ]);

    expect($response->get('id'))->toBe('new-pin');

    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && $request['board_id'] === 'board123'
            && $request['media_source']['source_type'] === 'image_url';
    });
});

test('it updates a pin', function () {
    Http::fake([
        'api.pinterest.com/v5/pins/pin123' => Http::response([
            'id' => 'pin123',
            'title' => 'Updated Pin',
        ], 200),
    ]);

    $response = $this->service->update('pin123', ['title' => 'Updated Pin']);

    expect($response->get('title'))->toBe('Updated Pin');
});

test('it deletes a pin', function () {
    Http::fake([
        'api.pinterest.com/v5/pins/pin123' => Http::response(null, 204),
    ]);

    $response = $this->service->delete('pin123');

    expect($response->status())->toBe(204);
});

test('it saves a pin to a board', function () {
    Http::fake([
        'api.pinterest.com/v5/pins/pin123/save' => Http::response([
            'id' => 'pin123',
            'board_id' => 'board456',
        ], 200),
    ]);

    $response = $this->service->save('pin123', [
        'board_id' => 'board456',
    ]);

    expect($response->get('board_id'))->toBe('board456');

    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && $request['board_id'] === 'board456';
    });
});
