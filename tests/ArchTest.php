<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('all source classes are in the Pinterest namespace')
    ->expect('Pinterest')
    ->toBeClasses()
    ->ignoring('Pinterest\Services\Concerns');

arch('exceptions extend the base PinterestApiException')
    ->expect('Pinterest\Exceptions')
    ->toExtend('Pinterest\Exceptions\PinterestApiException')
    ->ignoring('Pinterest\Exceptions\PinterestApiException');

arch('services use PinterestClient')
    ->expect('Pinterest\Services')
    ->toUse('Pinterest\Client\PinterestClient')
    ->ignoring('Pinterest\Services\Concerns');
