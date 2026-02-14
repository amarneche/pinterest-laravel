<?php

namespace Pinterest\Exceptions;

use Illuminate\Http\Client\Response;

class PinterestRateLimitException extends PinterestApiException
{
    protected ?int $retryAfter;

    protected ?int $rateLimitRemaining;

    public function __construct(
        string $message = 'Pinterest API rate limit exceeded',
        int $code = 429,
        ?Response $response = null,
        ?\Throwable $previous = null,
    ) {
        $this->retryAfter = $response ? (int) $response->header('X-RateLimit-Reset') : null;
        $this->rateLimitRemaining = $response ? (int) $response->header('X-RateLimit-Remaining') : null;

        parent::__construct($message, $code, $response, $previous);
    }

    /**
     * Get the number of seconds to wait before retrying.
     */
    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }

    /**
     * Get the remaining rate limit count.
     */
    public function getRateLimitRemaining(): ?int
    {
        return $this->rateLimitRemaining;
    }
}
