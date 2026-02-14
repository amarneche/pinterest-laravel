<?php

namespace Pinterest;

use Pinterest\Auth\OAuthService;
use Pinterest\Client\PinterestClient;
use Pinterest\Services\AdAccountsService;
use Pinterest\Services\AdGroupsService;
use Pinterest\Services\AdsService;
use Pinterest\Services\AudiencesService;
use Pinterest\Services\BoardsService;
use Pinterest\Services\CampaignsService;
use Pinterest\Services\ConversionsService;
use Pinterest\Services\CustomerListsService;
use Pinterest\Services\KeywordsService;
use Pinterest\Services\MediaService;
use Pinterest\Services\PinsService;
use Pinterest\Services\UserService;

/**
 * Pinterest SDK Manager.
 *
 * Main entry point for the Pinterest Laravel SDK.
 * Provides lazy-loaded access to all API service classes.
 *
 * Usage via Facade:
 *   Pinterest::boards()->list();
 *   Pinterest::pins()->create([...]);
 *   Pinterest::oauth()->getAuthorizationUrl();
 */
class Pinterest
{
    protected ?BoardsService $boardsService = null;

    protected ?PinsService $pinsService = null;

    protected ?UserService $userService = null;

    protected ?MediaService $mediaService = null;

    protected ?AdAccountsService $adAccountsService = null;

    protected ?CampaignsService $campaignsService = null;

    protected ?AdGroupsService $adGroupsService = null;

    protected ?AdsService $adsService = null;

    protected ?AudiencesService $audiencesService = null;

    protected ?ConversionsService $conversionsService = null;

    protected ?CustomerListsService $customerListsService = null;

    protected ?KeywordsService $keywordsService = null;

    public function __construct(
        protected PinterestClient $client,
        protected OAuthService $oauth,
    ) {}

    /**
     * Get the Boards service.
     */
    public function boards(): BoardsService
    {
        return $this->boardsService ??= new BoardsService($this->client);
    }

    /**
     * Get the Pins service.
     */
    public function pins(): PinsService
    {
        return $this->pinsService ??= new PinsService($this->client);
    }

    /**
     * Get the User service.
     */
    public function user(): UserService
    {
        return $this->userService ??= new UserService($this->client);
    }

    /**
     * Get the Media service.
     */
    public function media(): MediaService
    {
        return $this->mediaService ??= new MediaService($this->client);
    }

    /**
     * Get the Ad Accounts service.
     */
    public function adAccounts(): AdAccountsService
    {
        return $this->adAccountsService ??= new AdAccountsService($this->client);
    }

    /**
     * Get the Campaigns service.
     */
    public function campaigns(): CampaignsService
    {
        return $this->campaignsService ??= new CampaignsService($this->client);
    }

    /**
     * Get the Ad Groups service.
     */
    public function adGroups(): AdGroupsService
    {
        return $this->adGroupsService ??= new AdGroupsService($this->client);
    }

    /**
     * Get the Ads service.
     */
    public function ads(): AdsService
    {
        return $this->adsService ??= new AdsService($this->client);
    }

    /**
     * Get the Audiences service.
     */
    public function audiences(): AudiencesService
    {
        return $this->audiencesService ??= new AudiencesService($this->client);
    }

    /**
     * Get the Conversions service.
     */
    public function conversions(): ConversionsService
    {
        return $this->conversionsService ??= new ConversionsService($this->client);
    }

    /**
     * Get the Customer Lists service.
     */
    public function customerLists(): CustomerListsService
    {
        return $this->customerListsService ??= new CustomerListsService($this->client);
    }

    /**
     * Get the Keywords service.
     */
    public function keywords(): KeywordsService
    {
        return $this->keywordsService ??= new KeywordsService($this->client);
    }

    /**
     * Get the OAuth service for authorization and token management.
     */
    public function oauth(): OAuthService
    {
        return $this->oauth;
    }

    /**
     * Get the underlying HTTP client.
     */
    public function getClient(): PinterestClient
    {
        return $this->client;
    }

    /**
     * Set a new access token on the client.
     */
    public function setAccessToken(string $token): self
    {
        $this->client->setAccessToken($token);

        return $this;
    }
}
