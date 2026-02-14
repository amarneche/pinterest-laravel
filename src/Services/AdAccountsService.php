<?php

namespace Pinterest\Services;

use Pinterest\Client\PinterestClient;
use Pinterest\Services\Concerns\HasPagination;
use Pinterest\Support\ApiResponse;
use Pinterest\Support\PaginatedResponse;

/**
 * Service for interacting with the Pinterest Ad Accounts API.
 *
 * Provides listing and retrieval of ad accounts, plus analytics.
 *
 * @see https://developers.pinterest.com/docs/api/v5/#tag/Ad-accounts
 */
class AdAccountsService
{
    use HasPagination;

    public function __construct(
        protected PinterestClient $client,
    ) {}

    /**
     * List ad accounts the authenticated user has access to.
     *
     * @param  array  $params  Query parameters (include_shared_accounts, page_size, bookmark)
     */
    public function list(array $params = []): PaginatedResponse
    {
        return $this->paginatedGet('ad_accounts', $params);
    }

    /**
     * Get a specific ad account by ID.
     */
    public function get(string $adAccountId): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}");
    }

    /**
     * Get analytics for an ad account.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (start_date, end_date, columns, granularity, click_window_days, engagement_window_days, view_window_days, conversion_report_time)
     */
    public function getAnalytics(string $adAccountId, array $params = []): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/analytics", $params);
    }

    /**
     * Create a new ad account.
     *
     * @param  array  $data  Ad account data (name, owner_user_id, country)
     */
    public function create(array $data): ApiResponse
    {
        return $this->client->post('ad_accounts', $data);
    }
}
