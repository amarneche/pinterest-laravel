<?php

namespace Pinterest\Services;

use Pinterest\Client\PinterestClient;
use Pinterest\Services\Concerns\HasPagination;
use Pinterest\Support\ApiResponse;
use Pinterest\Support\PaginatedResponse;

/**
 * Service for interacting with the Pinterest Audiences API.
 *
 * Provides CRUD operations for audiences within ad accounts.
 *
 * @see https://developers.pinterest.com/docs/api/v5/#tag/Audiences
 */
class AudiencesService
{
    use HasPagination;

    public function __construct(
        protected PinterestClient $client,
    ) {}

    /**
     * List audiences for an ad account.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (entity_statuses, page_size, order, bookmark)
     */
    public function list(string $adAccountId, array $params = []): PaginatedResponse
    {
        return $this->paginatedGet("ad_accounts/{$adAccountId}/audiences", $params);
    }

    /**
     * Get a specific audience.
     */
    public function get(string $adAccountId, string $audienceId): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/audiences/{$audienceId}");
    }

    /**
     * Create an audience.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $data  Audience creation data (name, rule, audience_type, description)
     */
    public function create(string $adAccountId, array $data): ApiResponse
    {
        return $this->client->post("ad_accounts/{$adAccountId}/audiences", $data);
    }

    /**
     * Update an audience.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  string  $audienceId  The audience ID
     * @param  array  $data  Audience update data
     */
    public function update(string $adAccountId, string $audienceId, array $data): ApiResponse
    {
        return $this->client->patch("ad_accounts/{$adAccountId}/audiences/{$audienceId}", $data);
    }

    /**
     * Delete an audience.
     */
    public function delete(string $adAccountId, string $audienceId): ApiResponse
    {
        return $this->client->delete("ad_accounts/{$adAccountId}/audiences/{$audienceId}");
    }
}
