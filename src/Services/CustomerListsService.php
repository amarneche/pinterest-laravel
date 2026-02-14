<?php

namespace Pinterest\Services;

use Pinterest\Client\PinterestClient;
use Pinterest\Services\Concerns\HasPagination;
use Pinterest\Support\ApiResponse;
use Pinterest\Support\PaginatedResponse;

/**
 * Service for interacting with the Pinterest Customer Lists API.
 *
 * Provides CRUD operations for customer lists within ad accounts.
 *
 * @see https://developers.pinterest.com/docs/api/v5/#tag/Customer-lists
 */
class CustomerListsService
{
    use HasPagination;

    public function __construct(
        protected PinterestClient $client,
    ) {}

    /**
     * List customer lists for an ad account.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $params  Query parameters (page_size, order, bookmark)
     */
    public function list(string $adAccountId, array $params = []): PaginatedResponse
    {
        return $this->paginatedGet("ad_accounts/{$adAccountId}/customer_lists", $params);
    }

    /**
     * Get a specific customer list.
     */
    public function get(string $adAccountId, string $customerListId): ApiResponse
    {
        return $this->client->get("ad_accounts/{$adAccountId}/customer_lists/{$customerListId}");
    }

    /**
     * Create a customer list.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  array  $data  Customer list data (name, records, list_type, exceptions)
     */
    public function create(string $adAccountId, array $data): ApiResponse
    {
        return $this->client->post("ad_accounts/{$adAccountId}/customer_lists", $data);
    }

    /**
     * Update a customer list.
     *
     * @param  string  $adAccountId  The ad account ID
     * @param  string  $customerListId  The customer list ID
     * @param  array  $data  Customer list update data
     */
    public function update(string $adAccountId, string $customerListId, array $data): ApiResponse
    {
        return $this->client->patch("ad_accounts/{$adAccountId}/customer_lists/{$customerListId}", $data);
    }
}
