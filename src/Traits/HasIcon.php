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

trait HasIcon
{
    protected string | null $icon = null;

    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    public function getIcon(): string | null
    {
        return  $this->icon;
    }
}
