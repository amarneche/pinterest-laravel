<?php

use Illuminate\Http\Client\Response;
use Pinterest\Exceptions\PinterestApiException;
use Pinterest\Exceptions\PinterestAuthException;
use Pinterest\Exceptions\PinterestRateLimitException;
use Pinterest\Exceptions\PinterestValidationException;

function makeResponse(array $body, int $status, array $headers = []): Response
{
    $psrResponse = new \GuzzleHttp\Psr7\Response($status, $headers, json_encode($body));

    return new Response($psrResponse);
}

test('PinterestApiException captures response data', function () {
    $response = makeResponse(['message' => 'Server error', 'code' => 500], 500);
    $exception = new PinterestApiException('Server error', 500, $response);

    expect($exception->getMessage())->toBe('Server error')
        ->and($exception->getCode())->toBe(500)
        ->and($exception->getResponse())->toBe($response)
        ->and($exception->getErrorBody())->toBe(['message' => 'Server error', 'code' => 500])
        ->and($exception->getApiErrorCode())->toBe(500);
});

test('PinterestApiException works without response', function () {
    $exception = new PinterestApiException('Something failed', 0);

    expect($exception->getMessage())->toBe('Something failed')
        ->and($exception->getResponse())->toBeNull()
        ->and($exception->getErrorBody())->toBe([])
        ->and($exception->getApiErrorCode())->toBeNull();
});

test('PinterestAuthException extends PinterestApiException', function () {
    $exception = new PinterestAuthException;

    expect($exception)
        ->toBeInstanceOf(PinterestApiException::class)
        ->and($exception->getCode())->toBe(401)
        ->and($exception->getMessage())->toBe('Pinterest authentication failed');
});

test('PinterestRateLimitException captures rate limit headers', function () {
    $response = makeResponse(
        ['message' => 'Rate limit exceeded'],
        429,
        ['X-RateLimit-Remaining' => '0', 'X-RateLimit-Reset' => '120']
    );

    $exception = new PinterestRateLimitException('Rate limited', 429, $response);

    expect($exception)
        ->toBeInstanceOf(PinterestApiException::class)
        ->and($exception->getRetryAfter())->toBe(120)
        ->and($exception->getRateLimitRemaining())->toBe(0);
});

test('PinterestValidationException captures validation errors', function () {
    $response = makeResponse([
        'message' => 'Invalid request',
        'errors' => ['name' => 'Name is required', 'board_id' => 'Invalid board ID'],
    ], 400);

    $exception = new PinterestValidationException('Validation failed', 400, $response);

    expect($exception)
        ->toBeInstanceOf(PinterestApiException::class)
        ->and($exception->getValidationErrors())->toBe([
            'name' => 'Name is required',
            'board_id' => 'Invalid board ID',
        ]);
});

test('PinterestValidationException handles missing errors key', function () {
    $response = makeResponse(['message' => 'Bad request'], 400);
    $exception = new PinterestValidationException('Bad request', 400, $response);

    expect($exception->getValidationErrors())->toBe([]);
});

test('exception hierarchy is correct', function () {
    expect(new PinterestAuthException)
        ->toBeInstanceOf(PinterestApiException::class)
        ->and(new PinterestRateLimitException)
        ->toBeInstanceOf(PinterestApiException::class)
        ->and(new PinterestValidationException)
        ->toBeInstanceOf(PinterestApiException::class);
});
