<?php

// config for Pinterest Laravel SDK

return [

    /*
    |--------------------------------------------------------------------------
    | Pinterest API Credentials
    |--------------------------------------------------------------------------
    |
    | Your Pinterest App credentials. You can find these in the Pinterest
    | Developer portal at https://developers.pinterest.com/apps/
    |
    */

    'client_id' => env('PINTEREST_CLIENT_ID', ''),

    'client_secret' => env('PINTEREST_CLIENT_SECRET', ''),

    'redirect_uri' => env('PINTEREST_REDIRECT_URI', ''),

    /*
    |--------------------------------------------------------------------------
    | Access Tokens
    |--------------------------------------------------------------------------
    |
    | Access and refresh tokens for API authentication. These can be set
    | statically via environment variables or managed dynamically through
    | the OAuth flow.
    |
    */

    'access_token' => env('PINTEREST_ACCESS_TOKEN', ''),

    'refresh_token' => env('PINTEREST_REFRESH_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Base URL, version, and OAuth URL for the Pinterest API.
    |
    */

    'api_version' => env('PINTEREST_API_VERSION', 'v5'),

    'base_url' => env('PINTEREST_BASE_URL', 'https://api.pinterest.com'),

    'oauth_url' => env('PINTEREST_OAUTH_URL', 'https://www.pinterest.com/oauth/'),

    /*
    |--------------------------------------------------------------------------
    | OAuth Scopes
    |--------------------------------------------------------------------------
    |
    | Comma-separated list of OAuth scopes to request during authorization.
    | Available scopes: boards:read, boards:write, pins:read, pins:write,
    | user_accounts:read, ads:read, ads:write, catalogs:read, catalogs:write
    |
    */

    'scopes' => env('PINTEREST_SCOPES', 'boards:read,pins:read,user_accounts:read'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Settings
    |--------------------------------------------------------------------------
    |
    | Timeout and retry configuration for API requests.
    |
    */

    'timeout' => env('PINTEREST_TIMEOUT', 30),

    'retry' => [
        'times' => 3,
        'sleep' => 100, // milliseconds
    ],

];
