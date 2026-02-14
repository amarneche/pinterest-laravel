<?php

namespace Pinterest\Auth;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Value object representing a Pinterest OAuth access token.
 *
 * @implements Arrayable<string, mixed>
 */
class AccessToken implements Arrayable, JsonSerializable
{
    public function __construct(
        protected string $accessToken,
        protected string $refreshToken = '',
        protected string $tokenType = 'bearer',
        protected ?int $expiresIn = null,
        protected ?string $scope = null,
    ) {}

    /**
     * Create an AccessToken from an API token response.
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            accessToken: $data['access_token'] ?? '',
            refreshToken: $data['refresh_token'] ?? '',
            tokenType: $data['token_type'] ?? 'bearer',
            expiresIn: $data['expires_in'] ?? null,
            scope: $data['scope'] ?? null,
        );
    }

    /**
     * Get the access token string.
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Get the refresh token string.
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * Get the token type (e.g. "bearer").
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * Get the token lifetime in seconds.
     */
    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }

    /**
     * Get the granted scopes.
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * Determine if the token has a refresh token.
     */
    public function hasRefreshToken(): bool
    {
        return $this->refreshToken !== '';
    }

    /**
     * Get the token string for use as a bearer token.
     */
    public function __toString(): string
    {
        return $this->accessToken;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn,
            'scope' => $this->scope,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
