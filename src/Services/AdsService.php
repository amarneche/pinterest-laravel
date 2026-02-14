<?php

namespace Pinterest\Services;

use Pinterest\Client\PinterestClient;
use Pinterest\Services\Concerns\HasPagination;
use Pinterest\Support\ApiResponse;
use Pinterest\Support\PaginatedResponse;

/**
 * Service for interacting with the Pinterest Ads API.
 *
 * Provides CRUD operations for ads within ad accounts.
 *
 * @see https://developers.pinterest.com/docs/api/v5/#tag/Ads
 */
class AdsService
{
    use HasPagination;

    public function __construct(
        protected PinterestClient $client,
    ) {}

    /**
     * List ads for an ad account.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (campaign_ids, ad_group_ids, ad_ids, entity_statuses, page_size, order, bookmark)
     */
    public function list(string $adAccountId, array $params = []): PaginatedResponse
    {
        return $this->paginatedGet("ad_accounts/{$adAccountId}/ads", $params);
    }

    /**
     * Get a specific ad.
     */
    public function get(string $adAccountId, string $adId): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/ads/{$adId}");
    }

    /**
     * Create one or more ads.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $data  Ad creation data
     */
    public function create(string $adAccountId, array $data): ApiResponse
    {
        return $this->client->post("ad_accounts/{$adAccountId}/ads", $data);
    }

    /**
     * Update one or more ads.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $data  Ad update data
     */
    public function update(string $adAccountId, array $data): ApiResponse
    {
        return $this->client->patch("ad_accounts/{$adAccountId}/ads", $data);
    }

    /**
     * Get analytics for ads.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (ad_ids, start_date, end_date, columns, granularity)
     */
    public function getAnalytics(string $adAccountId, array $params = []): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/ads/analytics", $params);
    }
}
