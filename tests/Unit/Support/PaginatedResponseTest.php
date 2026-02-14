<?php

use Illuminate\Http\Client\Response;
use Pinterest\Support\PaginatedResponse;

function makePaginatedResponse(array $body = [], int $status = 200, ?\Closure $callback = null): PaginatedResponse
{
    $psrResponse = new \GuzzleHttp\Psr7\Response($status, [], json_encode($body));
    $response = new Response($psrResponse);

    return new PaginatedResponse($response, $callback);
}

test('it returns items from the response', function () {
    $response = makePaginatedResponse([
        'items' => [
            ['id' => '1', 'name' => 'Item 1'],
            ['id' => '2', 'name' => 'Item 2'],
        ],
    ]);

    expect($response->items())->toHaveCount(2)
        ->and($response->items()[0]['name'])->toBe('Item 1')
        ->and($response->count())->toBe(2);
});

test('it returns bookmark for pagination', function () {
    $response = makePaginatedResponse([
        'items' => [['id' => '1']],
        'bookmark' => 'next-page-cursor',
    ]);

    expect($response->bookmark())->toBe('next-page-cursor')
        ->and($response->hasMorePages())->toBeTrue();
});

test('it detects when there are no more pages', function () {
    $response = makePaginatedResponse([
        'items' => [['id' => '1']],
        'bookmark' => null,
    ]);

    expect($response->hasMorePages())->toBeFalse()
        ->and($response->bookmark())->toBeNull();
});

test('it handles empty bookmark string as no more pages', function () {
    $response = makePaginatedResponse([
        'items' => [['id' => '1']],
        'bookmark' => '',
    ]);

    expect($response->hasMorePages())->toBeFalse();
});

test('it returns empty items when none present', function () {
    $response = makePaginatedResponse([]);

    expect($response->items())->toBe([])
        ->and($response->count())->toBe(0);
});

test('it can fetch next page via callback', function () {
    $nextResponse = makePaginatedResponse([
        'items' => [['id' => '3']],
        'bookmark' => null,
    ]);

    $response = makePaginatedResponse(
        ['items' => [['id' => '1'], ['id' => '2']], 'bookmark' => 'page2'],
        200,
        function (string $bookmark) use ($nextResponse) {
            expect($bookmark)->toBe('page2');

            return $nextResponse;
        }
    );

    $next = $response->getNextPage();

    expect($next)
        ->toBeInstanceOf(PaginatedResponse::class)
        ->and($next->items())->toHaveCount(1)
        ->and($next->hasMorePages())->toBeFalse();
});

test('it returns null when no more pages to fetch', function () {
    $response = makePaginatedResponse([
        'items' => [['id' => '1']],
        'bookmark' => null,
    ]);

    expect($response->getNextPage())->toBeNull();
});
