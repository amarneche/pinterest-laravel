<?php

namespace Pinterest\Services;

use Pinterest\Client\PinterestClient;
use Pinterest\Services\Concerns\HasPagination;
use Pinterest\Support\ApiResponse;
use Pinterest\Support\PaginatedResponse;

/**
 * Service for interacting with the Pinterest User Account API.
 *
 * Provides access to the authenticated user's account information,
 * boards, pins, and analytics.
 *
 * @see https://developers.pinterest.com/docs/api/v5/#tag/User-account
 */
class UserService
{
    use HasPagination;

    public function __construct(
        protected PinterestClient $client,
    ) {}

    /**
     * Get the authenticated user's account information.
     *
     * @param  array  $params  Query parameters (ad_account_id)
     */
    public function getAccount(array $params = []): ApiResponse
    {
        return $this->client->get('user_account', $params);
    }

    /**
     * Get analytics for the authenticated user's account.
     *
     * @param  array  $params  Query parameters (start_date, end_date, from_claimed_content, pin_format, app_types, content_type, source, metric_types, split_field, ad_account_id)
     */
    public function getAnalytics(array $params = []): ApiResponse
    {
        return $this->client->get('user_account/analytics', $params);
    }

    /**
     * Get analytics for the authenticated user's top pins.
     *
     * @param  array  $params  Query parameters (start_date, end_date, sort_by, from_claimed_content, pin_format, app_types, content_type, source, metric_types, num_of_pins, created_in_last_n_days, ad_account_id)
     */
    public function getTopPinsAnalytics(array $params = []): ApiResponse
    {
        return $this->client->get('user_account/analytics/top_pins', $params);
    }

    /**
     * Get analytics for the authenticated user's top video pins.
     *
     * @param  array  $params  Query parameters (start_date, end_date, sort_by, from_claimed_content, pin_format, app_types, content_type, source, metric_types, num_of_pins, created_in_last_n_days, ad_account_id)
     */
    public function getTopVideoPinsAnalytics(array $params = []): ApiResponse
    {
        return $this->client->get('user_account/analytics/top_video_pins', $params);
    }

    /**
     * List boards owned by the authenticated user.
     *
     * @param  array  $params  Query parameters (privacy, page_size, bookmark)
     */
    public function listBoards(array $params = []): PaginatedResponse
    {
        return $this->paginatedGet('boards', $params);
    }

    /**
     * List pins owned by the authenticated user.
     *
     * @param  array  $params  Query parameters (pin_filter, include_protected_pins, pin_type, creative_types, ad_account_id, pin_metrics, page_size, bookmark)
     */
    public function listPins(array $params = []): PaginatedResponse
    {
        return $this->paginatedGet('pins', $params);
    }
}
