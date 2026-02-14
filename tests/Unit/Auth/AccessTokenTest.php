<?php

use Pinterest\Auth\AccessToken;

test('it can be created with constructor', function () {
    $token = new AccessToken(
        accessToken: 'pina_abc123',
        refreshToken: 'pinr_xyz789',
        tokenType: 'bearer',
        expiresIn: 3600,
        scope: 'boards:read,pins:read',
    );

    expect($token->getAccessToken())->toBe('pina_abc123')
        ->and($token->getRefreshToken())->toBe('pinr_xyz789')
        ->and($token->getTokenType())->toBe('bearer')
        ->and($token->getExpiresIn())->toBe(3600)
        ->and($token->getScope())->toBe('boards:read,pins:read')
        ->and($token->hasRefreshToken())->toBeTrue();
});

test('it can be created from API response', function () {
    $token = AccessToken::fromResponse([
        'access_token' => 'pina_abc123',
        'refresh_token' => 'pinr_xyz789',
        'token_type' => 'bearer',
        'expires_in' => 2592000,
        'scope' => 'boards:read',
    ]);

    expect($token->getAccessToken())->toBe('pina_abc123')
        ->and($token->getRefreshToken())->toBe('pinr_xyz789')
        ->and($token->getExpiresIn())->toBe(2592000);
});

test('it handles missing optional fields', function () {
    $token = AccessToken::fromResponse([
        'access_token' => 'pina_abc123',
    ]);

    expect($token->getAccessToken())->toBe('pina_abc123')
        ->and($token->getRefreshToken())->toBe('')
        ->and($token->hasRefreshToken())->toBeFalse()
        ->and($token->getExpiresIn())->toBeNull()
        ->and($token->getScope())->toBeNull();
});

test('it converts to string as access token', function () {
    $token = new AccessToken(accessToken: 'pina_abc123');

    expect((string) $token)->toBe('pina_abc123');
});

test('it converts to array', function () {
    $token = new AccessToken(
        accessToken: 'pina_abc123',
        refreshToken: 'pinr_xyz789',
        tokenType: 'bearer',
        expiresIn: 3600,
        scope: 'boards:read',
    );

    $array = $token->toArray();

    expect($array)->toBe([
        'access_token' => 'pina_abc123',
        'refresh_token' => 'pinr_xyz789',
        'token_type' => 'bearer',
        'expires_in' => 3600,
        'scope' => 'boards:read',
    ]);
});

test('it is JSON serializable', function () {
    $token = new AccessToken(accessToken: 'pina_abc123');

    $json = json_encode($token);

    expect(json_decode($json, true))->toHaveKey('access_token', 'pina_abc123');
});
