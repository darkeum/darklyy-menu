<?php

/*
* @name        DARKLYY-MENU
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Darkeum\Menu\Traits;

trait HasUrl
{
    protected string | null $url;

    public function getUrl(): string
    {
        return $this->url;
    }
}
