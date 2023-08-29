<?php

/*
* @name        DARKLYY-MENU
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Darkeum\Menu;

use Darkeum\Menu\Helpers\Str;
use Darkeum\Menu\Url;

class ActiveUrlChecker
{
    public static function check(string $url, string $requestUrl, string $rootUrl = '/'): bool
    {
        $url = Url::fromString($url);
        $requestUrl = Url::fromString($requestUrl);

        if ($url->getHost() !== $requestUrl->getHost()) {
            return false;
        }

        $rootUrl = Str::ensureLeft('/', $rootUrl);

        $rootUrl = Str::ensureRight('/', $rootUrl);

        $itemPath = Str::ensureRight('/', $url->getPath());

        if (! Str::startsWith($itemPath, $rootUrl)) {
            return false;
        }

        $matchPath = Str::ensureRight('/', $requestUrl->getPath());

        $itemPath = Str::removeFromStart($rootUrl, $itemPath);
        $matchPath = Str::removeFromStart($rootUrl, $matchPath);

        if ($matchPath === $itemPath && Str::startsWith($itemPath, $matchPath)) {
            return true;
        }

        return false;
    }
}
