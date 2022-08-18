<?php

/*
* @name        DARKLYY-MENU
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Darkeum\Menu\Facades;

use Boot\Support\Facades\Facade;

class Menu extends Facade
{
    /**
     * @see \Darkeum\Menu\Menu
     */
    protected static function getFacadeAccessor(): string
    {
        return 'menu';
    }
}
