<?php

namespace Pinterest\Services;

use Pinterest\Client\PinterestClient;
use Pinterest\Services\Concerns\HasPagination;
use Pinterest\Support\ApiResponse;
use Pinterest\Support\PaginatedResponse;

/**
 * Service for interacting with the Pinterest Conversions API.
 *
 * Provides functionality for sending conversion events
 * and managing conversion tags.
 *
 * @see https://developers.pinterest.com/docs/api/v5/#tag/Conversion-events
 * @see https://developers.pinterest.com/docs/api/v5/#tag/Conversion-tags
 */
class ConversionsService
{
    use HasPagination;

    public function __construct(
        protected PinterestClient $client,
    ) {}

    /**
     * Send conversion events for an ad account.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $data  Conversion event data (data: array of event objects)
     */
    public function sendEvents(string $adAccountId, array $data): ApiResponse
    {
        return $this->client->post("ad_accounts/{$adAccountId}/events", $data);
    }

    /**
     * List conversion tags for an ad account.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (filter_deleted)
     */
    public function listTags(string $adAccountId, array $params = []): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/conversion_tags", $params);
    }

    /**
     * Get a specific conversion tag.
     */
    public function getTag(string $adAccountId, string $conversionTagId): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/conversion_tags/{$conversionTagId}");
    }

    /**
     * Create a conversion tag.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $data  Conversion tag data (name, aem_enabled, md_frequency, aem_fnln_enabled, aem_ph_enabled, aem_em_enabled, aem_db_enabled, aem_loc_enabled)
     */
    public function createTag(string $adAccountId, array $data): ApiResponse
    {
        return $this->client->post("ad_accounts/{$adAccountId}/conversion_tags", $data);
    }

    /**
     * Get page visit conversion tag events for an ad account.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (page_size, order, bookmark)
     */
    public function getPageVisitEvents(string $adAccountId, array $params = []): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/conversion_tags/page_visit", $params);
    }
}
