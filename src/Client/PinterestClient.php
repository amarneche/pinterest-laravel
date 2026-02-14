<?php

namespace Pinterest\Client;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Pinterest\Exceptions\PinterestApiException;
use Pinterest\Exceptions\PinterestAuthException;
use Pinterest\Exceptions\PinterestRateLimitException;
use Pinterest\Exceptions\PinterestValidationException;
use Pinterest\Support\ApiResponse;

/**
 * Core HTTP client for the Pinterest API.
 *
 * Wraps Laravel's HTTP client with Pinterest-specific authentication,
 * error handling, rate limit awareness, and retry logic.
 */
class PinterestClient
{
    protected string $baseUrl;

    protected string $apiVersion;

    protected string $accessToken;

    protected int $timeout;

    protected int $retryTimes;

    protected int $retrySleep;

    public function __construct(
        string $baseUrl,
        string $apiVersion,
        string $accessToken = '',
        int $timeout = 30,
        int $retryTimes = 3,
        int $retrySleep = 100,
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiVersion = $apiVersion;
        $this->accessToken = $accessToken;
        $this->timeout = $timeout;
        $this->retryTimes = $retryTimes;
        $this->retrySleep = $retrySleep;
    }

    /**
     * Set the access token for subsequent requests.
     */
    public function setAccessToken(string $token): self
    {
        $this->accessToken = $token;

        return $this;
    }

    /**
     * Get the current access token.
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Get the fully qualified base URL including API version.
     */
    public function getBaseUrl(): string
    {
        return "{$this->baseUrl}/{$this->apiVersion}";
    }

    /**
     * Perform a GET request.
     */
    public function get(string $endpoint, array $query = []): ApiResponse
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    /**
     * Perform a POST request.
     */
    public function post(string $endpoint, array $data = []): ApiResponse
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    /**
     * Perform a PATCH request.
     */
    public function patch(string $endpoint, array $data = []): ApiResponse
    {
        return $this->request('PATCH', $endpoint, ['json' => $data]);
    }

    /**
     * Perform a PUT request.
     */
    public function put(string $endpoint, array $data = []): ApiResponse
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    /**
     * Perform a DELETE request.
     */
    public function delete(string $endpoint, array $data = []): ApiResponse
    {
        return $this->request('DELETE', $endpoint, ['json' => $data]);
    }

    /**
     * Execute an HTTP request and return a normalized response.
     *
     * @throws PinterestAuthException
     * @throws PinterestRateLimitException
     * @throws PinterestValidationException
     * @throws PinterestApiException
     */
    protected function request(string $method, string $endpoint, array $options = []): ApiResponse
    {
        $url = $this->buildUrl($endpoint);

        $response = $this->buildRequest()->send($method, $url, $options);

        $this->handleErrors($response);

        return new ApiResponse($response);
    }

    /**
     * Build a configured PendingRequest instance.
     */
    protected function buildRequest(): PendingRequest
    {
        $request = Http::timeout($this->timeout)
            ->retry(
                $this->retryTimes,
                $this->retrySleep,
                fn (\Exception $exception, PendingRequest $request): bool => $this->shouldRetry($exception),
            )
            ->withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => 'pinterest-laravel-sdk/1.0',
            ]);

        if ($this->accessToken) {
            $request = $request->withToken($this->accessToken);
        }

        return $request;
    }

    /**
     * Build the full URL for an API endpoint.
     */
    protected function buildUrl(string $endpoint): string
    {
        $endpoint = ltrim($endpoint, '/');

        return "{$this->getBaseUrl()}/{$endpoint}";
    }

    /**
     * Determine if a failed request should be retried.
     */
    protected function shouldRetry(\Exception $exception): bool
    {
        // Retry on server errors and rate limits
        if ($exception instanceof \Illuminate\Http\Client\RequestException) {
            $status = $exception->response->status();

            return $status >= 500 || $status === 429;
        }

        // Retry on connection timeouts
        return $exception instanceof \Illuminate\Http\Client\ConnectionException;
    }

    /**
     * Handle error responses by throwing typed exceptions.
     *
     * @throws PinterestAuthException
     * @throws PinterestRateLimitException
     * @throws PinterestValidationException
     * @throws PinterestApiException
     */
    protected function handleErrors(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        match ($response->status()) {
            401, 403 => throw new PinterestAuthException(
                message: 'Authentication failed. Check your Pinterest credentials.',
                code: $response->status(),
                response: $response,
            ),
            429 => throw new PinterestRateLimitException(
                message: 'Pinterest API rate limit exceeded.',
                code: 429,
                response: $response,
            ),
            400, 422 => throw new PinterestValidationException(
                message: 'Invalid request to Pinterest API.',
                code: $response->status(),
                response: $response,
            ),
            default => throw new PinterestApiException(
                message: 'Pinterest API request failed.',
                code: $response->status(),
                response: $response,
            ),
        };
    }
}
