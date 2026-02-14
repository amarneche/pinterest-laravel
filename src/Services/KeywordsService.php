<?php

namespace Pinterest\Services;

use Pinterest\Client\PinterestClient;
use Pinterest\Services\Concerns\HasPagination;
use Pinterest\Support\ApiResponse;
use Pinterest\Support\PaginatedResponse;

/**
 * Service for interacting with the Pinterest Keywords API.
 *
 * Provides CRUD operations for keywords within ad accounts.
 *
 * @see https://developers.pinterest.com/docs/api/v5/#tag/Keywords
 */
class KeywordsService
{
    use HasPagination;

    public function __construct(
        protected PinterestClient $client,
    ) {}

    /**
     * List keywords for an ad account and ad group.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (ad_group_id, page_size, bookmark, match_types)
     */
    public function list(string $adAccountId, array $params = []): PaginatedResponse
    {
        return $this->paginatedGet("ad_accounts/{$adAccountId}/keywords", $params);
    }

    /**
     * Create keywords for an ad group.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $data  Keywords data (keywords: array of keyword objects, parent_id)
     */
    public function create(string $adAccountId, array $data): ApiResponse
    {
        return $this->client->post("ad_accounts/{$adAccountId}/keywords", $data);
    }

    /**
     * Update keywords.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $data  Keywords update data
     */
    public function update(string $adAccountId, array $data): ApiResponse
    {
        return $this->client->patch("ad_accounts/{$adAccountId}/keywords", $data);
    }

    /**
     * Get keyword metrics for an ad account.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (keyword_ids)
     */
    public function getMetrics(string $adAccountId, array $params = []): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/keywords/metrics", $params);
    }
}
