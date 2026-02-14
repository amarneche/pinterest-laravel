<?php

namespace Pinterest\Services;

use Pinterest\Client\PinterestClient;
use Pinterest\Services\Concerns\HasPagination;
use Pinterest\Support\ApiResponse;
use Pinterest\Support\PaginatedResponse;

/**
 * Service for interacting with the Pinterest Campaigns API.
 *
 * Provides CRUD operations for campaigns within ad accounts,
 * including status management and analytics.
 *
 * @see https://developers.pinterest.com/docs/api/v5/#tag/Campaigns
 */
class CampaignsService
{
    use HasPagination;

    public function __construct(
        protected PinterestClient $client,
    ) {}

    /**
     * List campaigns for an ad account.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (campaign_ids, entity_statuses, page_size, order, bookmark)
     */
    public function list(string $adAccountId, array $params = []): PaginatedResponse
    {
        return $this->paginatedGet("ad_accounts/{$adAccountId}/campaigns", $params);
    }

    /**
     * Get a specific campaign.
     */
    public function get(string $adAccountId, string $campaignId): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/campaigns/{$campaignId}");
    }

    /**
     * Create one or more campaigns.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $data  Campaign creation data (array of campaign objects)
     */
    public function create(string $adAccountId, array $data): ApiResponse
    {
        return $this->client->post("ad_accounts/{$adAccountId}/campaigns", $data);
    }

    /**
     * Update one or more campaigns.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $data  Campaign update data (array of campaign objects with id)
     */
    public function update(string $adAccountId, array $data): ApiResponse
    {
        return $this->client->patch("ad_accounts/{$adAccountId}/campaigns", $data);
    }

    /**
     * Get analytics for campaigns.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (campaign_ids, start_date, end_date, columns, granularity)
     */
    public function getAnalytics(string $adAccountId, array $params = []): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/campaigns/analytics", $params);
    }
}
