<?php

namespace Pinterest\Services;

use Pinterest\Client\PinterestClient;
use Pinterest\Services\Concerns\HasPagination;
use Pinterest\Support\ApiResponse;
use Pinterest\Support\PaginatedResponse;

/**
 * Service for interacting with the Pinterest Ad Groups API.
 *
 * Provides CRUD operations for ad groups within ad accounts.
 *
 * @see https://developers.pinterest.com/docs/api/v5/#tag/Ad-groups
 */
class AdGroupsService
{
    use HasPagination;

    public function __construct(
        protected PinterestClient $client,
    ) {}

    /**
     * List ad groups for an ad account.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (campaign_ids, ad_group_ids, entity_statuses, page_size, order, bookmark)
     */
    public function list(string $adAccountId, array $params = []): PaginatedResponse
    {
        return $this->paginatedGet("ad_accounts/{$adAccountId}/ad_groups", $params);
    }

    /**
     * Get a specific ad group.
     */
    public function get(string $adAccountId, string $adGroupId): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/ad_groups/{$adGroupId}");
    }

    /**
     * Create one or more ad groups.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $data  Ad group creation data
     */
    public function create(string $adAccountId, array $data): ApiResponse
    {
        return $this->client->post("ad_accounts/{$adAccountId}/ad_groups", $data);
    }

    /**
     * Update one or more ad groups.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $data  Ad group update data
     */
    public function update(string $adAccountId, array $data): ApiResponse
    {
        return $this->client->patch("ad_accounts/{$adAccountId}/ad_groups", $data);
    }

    /**
     * Get analytics for ad groups.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (ad_group_ids, start_date, end_date, columns, granularity)
     */
    public function getAnalytics(string $adAccountId, array $params = []): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/ad_groups/analytics", $params);
    }
}
