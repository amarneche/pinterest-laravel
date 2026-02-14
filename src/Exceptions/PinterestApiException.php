<?php

namespace Pinterest\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class PinterestApiException extends Exception
{
    protected ?Response $response;

    protected array $errorBody;

    public function __construct(
        string $message = 'Pinterest API error',
        int $code = 0,
        ?Response $response = null,
        ?\Throwable $previous = null,
    ) {
        $this->response = $response;
        $this->errorBody = $response?->json() ?? [];

        $apiMessage = $this->errorBody['message'] ?? $message;

        parent::__construct($apiMessage, $code, $previous);
    }

    /**
     * Get the raw HTTP response, if available.
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Get the decoded error body from the API response.
     */
    public function getErrorBody(): array
    {
        return $this->errorBody;
    }

    /**
     * Get the Pinterest API error code, if present.
     */
    public function getApiErrorCode(): ?int
    {
        return $this->errorBody['code'] ?? null;
    }
}
