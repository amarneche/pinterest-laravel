<?php

namespace Pinterest\Exceptions;

class PinterestAuthException extends PinterestApiException
{
    public function __construct(
        string $message = 'Pinterest authentication failed',
        int $code = 401,
        ?\Illuminate\Http\Client\Response $response = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $response, $previous);
    }
}
