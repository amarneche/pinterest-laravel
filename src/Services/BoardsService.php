<?php

namespace Pinterest\Services;

use Pinterest\Client\PinterestClient;
use Pinterest\Services\Concerns\HasPagination;
use Pinterest\Support\ApiResponse;
use Pinterest\Support\PaginatedResponse;

/**
 * Service for interacting with the Pinterest Boards API.
 *
 * Provides CRUD operations for boards and board sections,
 * as well as listing pins within boards and sections.
 *
 * @see https://developers.pinterest.com/docs/api/v5/#tag/Boards
 */
class BoardsService
{
    use HasPagination;

    public function __construct(
        protected PinterestClient $client,
    ) {}

    /**
     * List boards owned by the authenticated user.
     *
     * @param  array  $params  Query parameters (privacy, page_size, bookmark)
     */
    public function list(array $params = []): PaginatedResponse
    {
        return $this->paginatedGet('boards', $params);
    }

    /**
     * Get a specific board by ID.
     */
    public function get(string $boardId): ApiResponse
    {
        return $this->client->get("boards/{$boardId}");
    }

    /**
     * Create a new board.
     *
     * @param  array  $data  Board data (name, description, privacy)
     */
    public function create(array $data): ApiResponse
    {
        return $this->client->post('boards', $data);
    }

    /**
     * Update a board.
     *
     * @param  string  $boardId  The board ID
     * @param  array  $data  Fields to update (name, description, privacy)
     */
    public function update(string $boardId, array $data): ApiResponse
    {
        return $this->client->patch("boards/{$boardId}", $data);
    }

    /**
     * Delete a board.
     */
    public function delete(string $boardId): ApiResponse
    {
        return $this->client->delete("boards/{$boardId}");
    }

    /**
     * List pins on a board.
     *
     * @param  string  $boardId  The board ID
     * @param  array  $params  Query parameters (page_size, bookmark)
     */
    public function listPins(string $boardId, array $params = []): PaginatedResponse
    {
        return $this->paginatedGet("boards/{$boardId}/pins", $params);
    }

    /**
     * List sections of a board.
     *
     * @param  string  $boardId  The board ID
     * @param  array  $params  Query parameters (page_size, bookmark)
     */
    public function listSections(string $boardId, array $params = []): PaginatedResponse
    {
        return $this->paginatedGet("boards/{$boardId}/sections", $params);
    }

    /**
     * Create a new board section.
     *
     * @param  string  $boardId  The board ID
     * @param  array  $data  Section data (name)
     */
    public function createSection(string $boardId, array $data): ApiResponse
    {
        return $this->client->post("boards/{$boardId}/sections", $data);
    }

    /**
     * Update a board section.
     *
     * @param  string  $boardId  The board ID
     * @param  string  $sectionId  The section ID
     * @param  array  $data  Fields to update (name)
     */
    public function updateSection(string $boardId, string $sectionId, array $data): ApiResponse
    {
        return $this->client->patch("boards/{$boardId}/sections/{$sectionId}", $data);
    }

    /**
     * Delete a board section.
     */
    public function deleteSection(string $boardId, string $sectionId): ApiResponse
    {
        return $this->client->delete("boards/{$boardId}/sections/{$sectionId}");
    }

    /**
     * List pins in a board section.
     *
     * @param  string  $boardId  The board ID
     * @param  string  $sectionId  The section ID
     * @param  array  $params  Query parameters (page_size, bookmark)
     */
    public function listSectionPins(string $boardId, string $sectionId, array $params = []): PaginatedResponse
    {
        return $this->paginatedGet("boards/{$boardId}/sections/{$sectionId}/pins", $params);
    }
}
