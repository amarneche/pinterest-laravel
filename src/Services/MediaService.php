<?php

namespace Pinterest\Services;

use Pinterest\Client\PinterestClient;
use Pinterest\Support\ApiResponse;

/**
 * Service for interacting with the Pinterest Media API.
 *
 * Handles media upload registration and status checking
 * for video and other media content.
 *
 * @see https://developers.pinterest.com/docs/api/v5/#tag/Media
 */
class MediaService
{
    public function __construct(
        protected PinterestClient $client,
    ) {}

    /**
     * Register a media upload.
     *
     * Returns upload parameters to use for uploading the media file.
     *
     * @param  array  $data  Media registration data (media_type)
     */
    public function register(array $data): ApiResponse
    {
        return $this->client->post('media', $data);
    }

    /**
     * Get the status of a media upload.
     *
     * @param  string  $mediaId  The media upload ID
     */
    public function getStatus(string $mediaId): ApiResponse
    {
        return $this->client->get("media/{$mediaId}");
    }

    /**
     * List media uploads.
     *
     * @param  array  $params  Query parameters (bookmark, page_size)
     */
    public function list(array $params = []): ApiResponse
    {
        return $this->client->get('media', $params);
    }
}
