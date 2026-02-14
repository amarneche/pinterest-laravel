<?php

namespace Pinterest\Services\Concerns;

use Closure;
use Illuminate\Http\Client\Response;
use Pinterest\Support\PaginatedResponse;

/**
 * Trait providing cursor-based pagination support for service classes.
 *
 * Pinterest uses "bookmark" tokens for pagination across all list endpoints.
 */
trait HasPagination
{
    /**
     * Execute a paginated GET request.
     *
     * @param  string  $endpoint  The API endpoint
     * @param  array  $params  Query parameters (page_size, bookmark, etc.)
     * @param  Closure|null  $nextPageFactory  Custom factory for fetching next pages
     */
    protected function paginatedGet(string $endpoint, array $params = [], ?Closure $nextPageFactory = null): PaginatedResponse
    {
        $response = $this->client->get($endpoint, $params);

        $nextPageCallback = $nextPageFactory ?? function (string $bookmark) use ($endpoint, $params): PaginatedResponse {
            $params['bookmark'] = $bookmark;

            return $this->paginatedGet($endpoint, $params);
        };

        return new PaginatedResponse(
            $this->getRawResponse($endpoint, $params),
            $nextPageCallback,
        );
    }

    /**
     * Get the raw HTTP response for building a PaginatedResponse.
     */
    protected function getRawResponse(string $endpoint, array $params = []): Response
    {
        $url = $this->client->getBaseUrl().'/'.ltrim($endpoint, '/');

        return \Illuminate\Support\Facades\Http::timeout(config('pinterest.timeout', 30))
            ->withToken($this->client->getAccessToken())
            ->withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => 'pinterest-laravel-sdk/1.0',
            ])
            ->get($url, $params);
    }
}
