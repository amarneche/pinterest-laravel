<?php

namespace Pinterest\Support;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use JsonSerializable;

/**
 * Normalized response wrapper for Pinterest API responses.
 *
 * @implements ArrayAccess<string, mixed>
 * @implements Arrayable<string, mixed>
 */
class ApiResponse implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    protected array $data;

    protected int $statusCode;

    protected array $headers;

    public function __construct(Response $response)
    {
        $this->data = $response->json() ?? [];
        $this->statusCode = $response->status();
        $this->headers = $response->headers();
    }

    /**
     * Get the full decoded JSON body.
     */
    public function json(): array
    {
        return $this->data;
    }

    /**
     * Get the HTTP status code.
     */
    public function status(): int
    {
        return $this->statusCode;
    }

    /**
     * Determine if the response was successful.
     */
    public function successful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Get a value from the response using dot notation.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * Get the response headers.
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Get a specific header value.
     */
    public function header(string $name): ?string
    {
        $values = $this->headers[strtolower($name)] ?? $this->headers[$name] ?? null;

        if (is_array($values)) {
            return $values[0] ?? null;
        }

        return $values;
    }

    /**
     * Convert the response to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Convert the response to JSON.
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->data, $options);
    }

    /**
     * Specify data which should be serialized to JSON.
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }

    /**
     * Determine if an offset exists.
     */
    public function offsetExists(mixed $offset): bool
    {
        return Arr::has($this->data, $offset);
    }

    /**
     * Get a value at the given offset.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Set a value at the given offset.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        Arr::set($this->data, $offset, $value);
    }

    /**
     * Unset a value at the given offset.
     */
    public function offsetUnset(mixed $offset): void
    {
        Arr::forget($this->data, $offset);
    }
}
