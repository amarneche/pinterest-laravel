<?php

use Illuminate\Http\Client\Response;
use Pinterest\Support\ApiResponse;

function makeApiResponse(array $body = [], int $status = 200, array $headers = []): ApiResponse
{
    $psrResponse = new \GuzzleHttp\Psr7\Response($status, $headers, json_encode($body));
    $response = new Response($psrResponse);

    return new ApiResponse($response);
}

test('it wraps response data', function () {
    $response = makeApiResponse(['id' => '123', 'name' => 'Test']);

    expect($response->json())->toBe(['id' => '123', 'name' => 'Test'])
        ->and($response->status())->toBe(200)
        ->and($response->successful())->toBeTrue();
});

test('it supports dot notation access', function () {
    $response = makeApiResponse([
        'data' => [
            'user' => [
                'name' => 'John',
            ],
        ],
    ]);

    expect($response->get('data.user.name'))->toBe('John')
        ->and($response->get('data.missing', 'default'))->toBe('default');
});

test('it converts to array', function () {
    $data = ['id' => '1', 'name' => 'Test'];
    $response = makeApiResponse($data);

    expect($response->toArray())->toBe($data);
});

test('it converts to JSON', function () {
    $data = ['id' => '1'];
    $response = makeApiResponse($data);

    expect($response->toJson())->toBe('{"id":"1"}');
});

test('it is JSON serializable', function () {
    $data = ['id' => '1'];
    $response = makeApiResponse($data);

    expect(json_encode($response))->toBe('{"id":"1"}');
});

test('it supports array access', function () {
    $response = makeApiResponse(['name' => 'Test', 'id' => '1']);

    expect(isset($response['name']))->toBeTrue()
        ->and($response['name'])->toBe('Test')
        ->and(isset($response['missing']))->toBeFalse();
});

test('it detects unsuccessful responses', function () {
    $response = makeApiResponse(['error' => 'not found'], 404);

    expect($response->successful())->toBeFalse()
        ->and($response->status())->toBe(404);
});
