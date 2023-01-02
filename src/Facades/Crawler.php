<?php

namespace GateGem\Crawler\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * 
 * 
 * @method static mixed insertLink($link)
 * @method static bool checkLink($link)
 * @method static mixed checkKeyOrCreateTabale($domain_key)
 * @see \GateGem\Crawler\Facades\Crawler
 */
class Crawler extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \GateGem\Crawler\CrawlerManager::class;
    }
}
