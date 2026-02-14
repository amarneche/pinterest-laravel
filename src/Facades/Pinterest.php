<?php

namespace Pinterest\Facades;

use Illuminate\Support\Facades\Facade;
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
 * Pinterest SDK Facade.
 *
 * @method static BoardsService boards()
 * @method static PinsService pins()
 * @method static UserService user()
 * @method static MediaService media()
 * @method static AdAccountsService adAccounts()
 * @method static CampaignsService campaigns()
 * @method static AdGroupsService adGroups()
 * @method static AdsService ads()
 * @method static AudiencesService audiences()
 * @method static ConversionsService conversions()
 * @method static CustomerListsService customerLists()
 * @method static KeywordsService keywords()
 * @method static OAuthService oauth()
 * @method static PinterestClient getClient()
 * @method static \Pinterest\Pinterest setAccessToken(string $token)
 *
 * @see \Pinterest\Pinterest
 */
class Pinterest extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Pinterest\Pinterest::class;
    }
}
