<?php

namespace Pinterest\Services;

use Pinterest\Client\PinterestClient;
use Pinterest\Services\Concerns\HasPagination;
use Pinterest\Support\ApiResponse;
use Pinterest\Support\PaginatedResponse;

/**
 * Service for interacting with the Pinterest Pins API.
 *
 * Provides CRUD operations for pins, saving pins to boards,
 * and listing pins owned by the authenticated user.
 *
 * @see https://developers.pinterest.com/docs/api/v5/#tag/Pins
 */
class PinsService
{
    use HasPagination;

    public function __construct(
        protected PinterestClient $client,
    ) {}

    /**
     * List pins owned by the authenticated user.
     *
     * @param  array  $params  Query parameters (pin_filter, include_protected_pins, pin_type, creative_types, ad_account_id, pin_metrics, page_size, bookmark)
     */
    public function list(array $params = []): PaginatedResponse
    {
        return $this->paginatedGet('pins', $params);
    }

    /**
     * Get a specific pin by ID.
     *
     * @param  string  $pinId  The pin ID
     * @param  array  $params  Query parameters (pin_metrics, ad_account_id)
     */
    public function get(string $pinId, array $params = []): ApiResponse
    {
        return $this->client->get("pins/{$pinId}", $params);
    }

    /**
     * Create a new pin.
     *
     * @param  array  $data  Pin data including:
     *                       - board_id (required): The board to pin to
     *                       - media_source (required): Media source object
     *                       - title: Pin title (<= 100 chars)
     *                       - description: Pin description (<= 500 chars)
     *                       - link: Redirect link (<= 2048 chars)
     *                       - dominant_color: Hex color (e.g. "#6E7874")
     *                       - alt_text: Alt text (<= 500 chars)
     *                       - board_section_id: Target board section
     *                       - parent_pin_id: Source pin if saving
     */
    public function create(array $data): ApiResponse
    {
        return $this->client->post('pins', $data);
    }

    /**
     * Update a pin.
     *
     * @param  string  $pinId  The pin ID
     * @param  array  $data  Fields to update
     */
    public function update(string $pinId, array $data): ApiResponse
    {
        return $this->client->patch("pins/{$pinId}", $data);
    }

    /**
     * Delete a pin.
     */
    public function delete(string $pinId): ApiResponse
    {
        return $this->client->delete("pins/{$pinId}");
    }

    /**
     * Save a pin to a board.
     *
     * @param  string  $pinId  The pin ID to save
     * @param  array  $data  Save data (board_id, board_section_id)
     */
    public function save(string $pinId, array $data): ApiResponse
    {
        return $this->client->post("pins/{$pinId}/save", $data);
    }
}
