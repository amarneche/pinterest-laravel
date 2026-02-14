<?php

use Illuminate\Support\Facades\Http;
use Pinterest\Client\PinterestClient;
use Pinterest\Exceptions\PinterestApiException;
use Pinterest\Exceptions\PinterestAuthException;
use Pinterest\Exceptions\PinterestRateLimitException;
use Pinterest\Exceptions\PinterestValidationException;
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
});

test('it builds the correct base URL', function () {
    expect($this->client->getBaseUrl())->toBe('https://api.pinterest.com/v5');
});

test('it can set and get access token', function () {
    $this->client->setAccessToken('new-token');
    expect($this->client->getAccessToken())->toBe('new-token');
});

test('it performs a GET request with query parameters', function () {
    Http::fake([
        'api.pinterest.com/v5/boards*' => Http::response([
            'items' => [['id' => '123', 'name' => 'Test Board']],
        ], 200),
    ]);

    $response = $this->client->get('boards', ['page_size' => 10]);

    expect($response)
        ->toBeInstanceOf(ApiResponse::class)
        ->and($response->status())->toBe(200)
        ->and($response->successful())->toBeTrue()
        ->and($response->get('items.0.name'))->toBe('Test Board');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'api.pinterest.com/v5/boards')
            && $request->hasHeader('Authorization', 'Bearer test-token');
    });
});

test('it performs a POST request with JSON data', function () {
    Http::fake([
        'api.pinterest.com/v5/boards' => Http::response([
            'id' => '456',
            'name' => 'New Board',
        ], 201),
    ]);

    $response = $this->client->post('boards', [
        'name' => 'New Board',
        'description' => 'A test board',
    ]);

    expect($response->status())->toBe(201)
        ->and($response->get('name'))->toBe('New Board');

    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && $request['name'] === 'New Board';
    });
});

test('it performs a PATCH request', function () {
    Http::fake([
        'api.pinterest.com/v5/boards/123' => Http::response([
            'id' => '123',
            'name' => 'Updated Board',
        ], 200),
    ]);

    $response = $this->client->patch('boards/123', ['name' => 'Updated Board']);

    expect($response->get('name'))->toBe('Updated Board');

    Http::assertSent(function ($request) {
        return $request->method() === 'PATCH';
    });
});

test('it performs a DELETE request', function () {
    Http::fake([
        'api.pinterest.com/v5/boards/123' => Http::response(null, 204),
    ]);

    $response = $this->client->delete('boards/123');

    expect($response->status())->toBe(204);

    Http::assertSent(function ($request) {
        return $request->method() === 'DELETE';
    });
});

test('it throws PinterestAuthException on 401', function () {
    Http::fake([
        'api.pinterest.com/v5/boards' => Http::response([
            'code' => 401,
            'message' => 'Unauthorized',
        ], 401),
    ]);

    $this->client->get('boards');
})->throws(PinterestAuthException::class);

test('it throws PinterestAuthException on 403', function () {
    Http::fake([
        'api.pinterest.com/v5/boards' => Http::response([
            'code' => 403,
            'message' => 'Forbidden',
        ], 403),
    ]);

    $this->client->get('boards');
})->throws(PinterestAuthException::class);

test('it throws PinterestRateLimitException on 429', function () {
    Http::fake([
        'api.pinterest.com/v5/boards' => Http::response([
            'code' => 429,
            'message' => 'Rate limit exceeded',
        ], 429, [
            'X-RateLimit-Remaining' => '0',
            'X-RateLimit-Reset' => '60',
        ]),
    ]);

    try {
        $this->client->get('boards');
        $this->fail('Expected PinterestRateLimitException');
    } catch (PinterestRateLimitException $e) {
        expect($e->getCode())->toBe(429)
            ->and($e->getRetryAfter())->toBe(60)
            ->and($e->getRateLimitRemaining())->toBe(0);
    }
});

test('it throws PinterestValidationException on 400', function () {
    Http::fake([
        'api.pinterest.com/v5/boards' => Http::response([
            'code' => 400,
            'message' => 'Invalid request',
            'errors' => ['name' => 'Name is required'],
        ], 400),
    ]);

    $this->client->post('boards', []);
})->throws(PinterestValidationException::class);

test('it throws PinterestApiException on 500', function () {
    Http::fake([
        'api.pinterest.com/v5/boards' => Http::response([
            'message' => 'Internal server error',
        ], 500),
    ]);

    $this->client->get('boards');
})->throws(PinterestApiException::class);

test('it sends correct user agent header', function () {
    Http::fake([
        'api.pinterest.com/v5/boards' => Http::response(['items' => []], 200),
    ]);

    $this->client->get('boards');

    Http::assertSent(function ($request) {
        return $request->hasHeader('User-Agent', 'pinterest-laravel-sdk/1.0')
            && $request->hasHeader('Accept', 'application/json');
    });
});
