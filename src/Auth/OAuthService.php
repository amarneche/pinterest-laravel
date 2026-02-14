<?php

namespace Pinterest\Auth;

use Illuminate\Support\Facades\Http;
use Pinterest\Exceptions\PinterestAuthException;

/**
 * OAuth2 service for Pinterest authorization and token management.
 *
 * Supports the full OAuth2 authorization code flow:
 * 1. Generate an authorization URL
 * 2. Exchange the authorization code for tokens
 * 3. Refresh expired access tokens
 */
class OAuthService
{
    protected string $clientId;

    protected string $clientSecret;

    protected string $redirectUri;

    protected string $oauthUrl;

    protected string $baseUrl;

    protected string $apiVersion;

    protected string $scopes;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        string $oauthUrl,
        string $baseUrl,
        string $apiVersion,
        string $scopes = '',
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->oauthUrl = rtrim($oauthUrl, '/');
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiVersion = $apiVersion;
        $this->scopes = $scopes;
    }

    /**
     * Generate the Pinterest OAuth authorization URL.
     *
     * Redirect users to this URL to begin the OAuth flow.
     *
     * @param  string|null  $state  An opaque string for CSRF protection
     * @param  string|null  $scopes  Override the default scopes (comma-separated)
     */
    public function getAuthorizationUrl(?string $state = null, ?string $scopes = null): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => $scopes ?? $this->scopes,
        ];

        if ($state) {
            $params['state'] = $state;
        }

        return $this->oauthUrl.'?'.http_build_query($params);
    }

    /**
     * Exchange an authorization code for an access token.
     *
     * @param  string  $code  The authorization code received from the OAuth callback
     *
     * @throws PinterestAuthException
     */
    public function exchangeCodeForToken(string $code): AccessToken
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$this->getBasicAuthToken(),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post("{$this->baseUrl}/{$this->apiVersion}/oauth/token", [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
        ]);

        if ($response->status() === 401) {
            throw new PinterestAuthException(
                message: 'OAuth token exchange failed. Check your client credentials.',
                code: 401,
                response: $response,
            );
        }

        if (! $response->successful()) {
            throw new PinterestAuthException(
                message: 'Failed to exchange authorization code for access token.',
                code: $response->status(),
                response: $response,
            );
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            throw new PinterestAuthException(
                message: 'No access_token found in token exchange response.',
                code: $response->status(),
                response: $response,
            );
        }

        return AccessToken::fromResponse($data);
    }

    /**
     * Refresh an access token using a refresh token.
     *
     * Mirrors the Python SDK's `get_new_access_token` function.
     *
     * @param  string|null  $refreshToken  Refresh token. Falls back to config value if not provided.
     *
     * @throws PinterestAuthException
     */
    public function refreshToken(?string $refreshToken = null): AccessToken
    {
        $refreshToken = $refreshToken ?: config('pinterest.refresh_token');

        if (empty($refreshToken)) {
            throw new PinterestAuthException(
                message: 'No refresh token provided and none configured.',
                code: 0,
            );
        }

        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$this->getBasicAuthToken(),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post("{$this->baseUrl}/{$this->apiVersion}/oauth/token", [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        if ($response->status() === 401) {
            throw new PinterestAuthException(
                message: 'Token refresh failed. Check your credentials and refresh token.',
                code: 401,
                response: $response,
            );
        }

        if (! $response->successful()) {
            throw new PinterestAuthException(
                message: 'Failed to refresh access token.',
                code: $response->status(),
                response: $response,
            );
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            throw new PinterestAuthException(
                message: 'No access_token found in token refresh response.',
                code: $response->status(),
                response: $response,
            );
        }

        return AccessToken::fromResponse($data);
    }

    /**
     * Generate the Base64-encoded Basic auth token.
     *
     * Mirrors the Python SDK pattern:
     * base64_encode(f"{app_id}:{app_secret}")
     */
    protected function getBasicAuthToken(): string
    {
        return base64_encode("{$this->clientId}:{$this->clientSecret}");
    }
}
