<?php

namespace Pinterest\Support;

use Closure;
use Illuminate\Http\Client\Response;

/**
 * Response wrapper for paginated Pinterest API list endpoints.
 *
 * Pinterest uses cursor-based pagination via "bookmark" tokens.
 */
class PaginatedResponse extends ApiResponse
{
    /**
     * Callback to fetch the next page of results.
     */
    protected ?Closure $nextPageCallback;

    public function __construct(Response $response, ?Closure $nextPageCallback = null)
    {
        parent::__construct($response);
        $this->nextPageCallback = $nextPageCallback;
    }

    /**
     * Get the list of items from the response.
     */
    public function items(): array
    {
        return $this->get('items', []);
    }

    /**
     * Get the bookmark token for the next page.
     */
    public function bookmark(): ?string
    {
        $bookmark = $this->get('bookmark');

        return $bookmark ?: null;
    }

    /**
     * Determine if there are more pages available.
     */
    public function hasMorePages(): bool
    {
        return $this->bookmark() !== null;
    }

    /**
     * Fetch the next page of results.
     *
     * Returns null if there are no more pages.
     */
    public function getNextPage(): ?self
    {
        if (! $this->hasMorePages() || ! $this->nextPageCallback) {
            return null;
        }

        return ($this->nextPageCallback)($this->bookmark());
    }

    /**
     * Get the total count of items in the current page.
     */
    public function count(): int
    {
        return count($this->items());
    }
}
