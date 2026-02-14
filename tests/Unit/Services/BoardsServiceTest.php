<?php

use Illuminate\Support\Facades\Http;
use Pinterest\Client\PinterestClient;
use Pinterest\Services\BoardsService;
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
    $this->service = new BoardsService($this->client);
});

test('it lists boards with pagination', function () {
    Http::fake([
        'api.pinterest.com/v5/boards*' => Http::response([
            'items' => [
                ['id' => '1', 'name' => 'Board 1'],
                ['id' => '2', 'name' => 'Board 2'],
            ],
            'bookmark' => 'next-page-token',
        ], 200),
    ]);

    $response = $this->service->list(['page_size' => 25]);

    expect($response)
        ->toBeInstanceOf(PaginatedResponse::class)
        ->and($response->items())->toHaveCount(2)
        ->and($response->bookmark())->toBe('next-page-token')
        ->and($response->hasMorePages())->toBeTrue();
});

test('it gets a specific board', function () {
    Http::fake([
        'api.pinterest.com/v5/boards/123' => Http::response([
            'id' => '123',
            'name' => 'My Board',
            'description' => 'Test description',
            'privacy' => 'PUBLIC',
        ], 200),
    ]);

    $response = $this->service->get('123');

    expect($response)
        ->toBeInstanceOf(ApiResponse::class)
        ->and($response->get('name'))->toBe('My Board')
        ->and($response->get('privacy'))->toBe('PUBLIC');
});

test('it creates a board', function () {
    Http::fake([
        'api.pinterest.com/v5/boards' => Http::response([
            'id' => '456',
            'name' => 'New Board',
        ], 201),
    ]);

    $response = $this->service->create([
        'name' => 'New Board',
        'description' => 'A new board',
        'privacy' => 'PUBLIC',
    ]);

    expect($response->get('id'))->toBe('456');

    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && $request['name'] === 'New Board';
    });
});

test('it updates a board', function () {
    Http::fake([
        'api.pinterest.com/v5/boards/123' => Http::response([
            'id' => '123',
            'name' => 'Updated Board',
        ], 200),
    ]);

    $response = $this->service->update('123', ['name' => 'Updated Board']);

    expect($response->get('name'))->toBe('Updated Board');
});

test('it deletes a board', function () {
    Http::fake([
        'api.pinterest.com/v5/boards/123' => Http::response(null, 204),
    ]);

    $response = $this->service->delete('123');

    expect($response->status())->toBe(204);
});

test('it lists pins on a board', function () {
    Http::fake([
        'api.pinterest.com/v5/boards/123/pins*' => Http::response([
            'items' => [
                ['id' => 'pin1', 'title' => 'Pin 1'],
            ],
            'bookmark' => null,
        ], 200),
    ]);

    $response = $this->service->listPins('123');

    expect($response->items())->toHaveCount(1)
        ->and($response->hasMorePages())->toBeFalse();
});

test('it creates a board section', function () {
    Http::fake([
        'api.pinterest.com/v5/boards/123/sections' => Http::response([
            'id' => 'sec1',
            'name' => 'New Section',
        ], 201),
    ]);

    $response = $this->service->createSection('123', ['name' => 'New Section']);

    expect($response->get('name'))->toBe('New Section');
});

test('it updates a board section', function () {
    Http::fake([
        'api.pinterest.com/v5/boards/123/sections/sec1' => Http::response([
            'id' => 'sec1',
            'name' => 'Updated Section',
        ], 200),
    ]);

    $response = $this->service->updateSection('123', 'sec1', ['name' => 'Updated Section']);

    expect($response->get('name'))->toBe('Updated Section');
});

test('it deletes a board section', function () {
    Http::fake([
        'api.pinterest.com/v5/boards/123/sections/sec1' => Http::response(null, 204),
    ]);

    $response = $this->service->deleteSection('123', 'sec1');

    expect($response->status())->toBe(204);
});

test('it lists sections of a board', function () {
    Http::fake([
        'api.pinterest.com/v5/boards/123/sections*' => Http::response([
            'items' => [
                ['id' => 'sec1', 'name' => 'Section 1'],
            ],
            'bookmark' => null,
        ], 200),
    ]);

    $response = $this->service->listSections('123');

    expect($response->items())->toHaveCount(1);
});
