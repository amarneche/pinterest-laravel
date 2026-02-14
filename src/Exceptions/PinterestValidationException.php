<?php

namespace Pinterest\Exceptions;

use Illuminate\Http\Client\Response;

class PinterestValidationException extends PinterestApiException
{
    protected array $validationErrors;

    public function __construct(
        string $message = 'Pinterest API validation error',
        int $code = 400,
        ?Response $response = null,
        ?\Throwable $previous = null,
    ) {
        $body = $response?->json() ?? [];
        $this->validationErrors = $body['errors'] ?? $body['details'] ?? [];

        parent::__construct($message, $code, $response, $previous);
    }

    /**
     * Get the validation errors from the API response.
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
