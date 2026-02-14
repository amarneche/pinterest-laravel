<?php

use Illuminate\Support\Facades\Http;
use Pinterest\Auth\AccessToken;
use Pinterest\Auth\OAuthService;
use Pinterest\Exceptions\PinterestAuthException;

beforeEach(function () {
    $this->oauth = new OAuthService(
        clientId: 'test-client-id',
        clientSecret: 'test-client-secret',
        redirectUri: 'https://example.com/callback',
        oauthUrl: 'https://www.pinterest.com/oauth/',
        baseUrl: 'https://api.pinterest.com',
        apiVersion: 'v5',
        scopes: 'boards:read,pins:read',
    );
});

test('it generates a correct authorization URL', function () {
    $url = $this->oauth->getAuthorizationUrl('random-state');

    expect($url)
        ->toContain('https://www.pinterest.com/oauth')
        ->toContain('client_id=test-client-id')
        ->toContain('redirect_uri='.urlencode('https://example.com/callback'))
        ->toContain('response_type=code')
        ->toContain('scope=boards%3Aread%2Cpins%3Aread')
        ->toContain('state=random-state');
});

test('it generates authorization URL without state', function () {
    $url = $this->oauth->getAuthorizationUrl();

    expect($url)
        ->toContain('client_id=test-client-id')
        ->not->toContain('state=');
});

test('it allows overriding scopes in authorization URL', function () {
    $url = $this->oauth->getAuthorizationUrl(scopes: 'boards:write,pins:write');

    expect($url)->toContain('scope=boards%3Awrite%2Cpins%3Awrite');
});

test('it exchanges authorization code for access token', function () {
    Http::fake([
        'api.pinterest.com/v5/oauth/token' => Http::response([
            'access_token' => 'pina_new_token',
            'refresh_token' => 'pinr_new_refresh',
            'token_type' => 'bearer',
            'expires_in' => 2592000,
            'scope' => 'boards:read,pins:read',
        ], 200),
    ]);

    $token = $this->oauth->exchangeCodeForToken('auth-code-123');

    expect($token)
        ->toBeInstanceOf(AccessToken::class)
        ->and($token->getAccessToken())->toBe('pina_new_token')
        ->and($token->getRefreshToken())->toBe('pinr_new_refresh')
        ->and($token->getExpiresIn())->toBe(2592000);

    Http::assertSent(function ($request) {
        $authHeader = $request->header('Authorization')[0] ?? '';
        $expectedAuth = 'Basic '.base64_encode('test-client-id:test-client-secret');

        return $authHeader === $expectedAuth
            && $request['grant_type'] === 'authorization_code'
            && $request['code'] === 'auth-code-123'
            && $request['redirect_uri'] === 'https://example.com/callback';
    });
});

test('it throws on failed code exchange with 401', function () {
    Http::fake([
        'api.pinterest.com/v5/oauth/token' => Http::response([
            'error' => 'invalid_client',
        ], 401),
    ]);

    $this->oauth->exchangeCodeForToken('bad-code');
})->throws(PinterestAuthException::class, 'OAuth token exchange failed');

test('it throws when no access_token in exchange response', function () {
    Http::fake([
        'api.pinterest.com/v5/oauth/token' => Http::response([
            'error' => 'something_weird',
        ], 200),
    ]);

    $this->oauth->exchangeCodeForToken('some-code');
})->throws(PinterestAuthException::class, 'No access_token found');

test('it refreshes an access token', function () {
    Http::fake([
        'api.pinterest.com/v5/oauth/token' => Http::response([
            'access_token' => 'pina_refreshed_token',
            'refresh_token' => 'pinr_new_refresh',
            'token_type' => 'bearer',
            'expires_in' => 2592000,
        ], 200),
    ]);

    $token = $this->oauth->refreshToken('my-refresh-token');

    expect($token->getAccessToken())->toBe('pina_refreshed_token');

    Http::assertSent(function ($request) {
        return $request['grant_type'] === 'refresh_token'
            && $request['refresh_token'] === 'my-refresh-token';
    });
});

test('it throws when no refresh token is provided or configured', function () {
    config(['pinterest.refresh_token' => '']);

    $this->oauth->refreshToken(null);
})->throws(PinterestAuthException::class, 'No refresh token provided');

test('it throws on failed token refresh with 401', function () {
    Http::fake([
        'api.pinterest.com/v5/oauth/token' => Http::response([
            'error' => 'invalid_grant',
        ], 401),
    ]);

    $this->oauth->refreshToken('expired-refresh-token');
})->throws(PinterestAuthException::class, 'Token refresh failed');
