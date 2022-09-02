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

trait HasBadge{

    protected string | int | float | null $badge = null;
    protected string | null $badgeClass = null;

    public function setBadge($badge, $class = null)
    {
        $this->badge = $badge;
        $this->badgeClass = $class;
        return $this;
    }

    public function getBadge(): string | null
    {
        return  $this->badge;
    }

    public function getBadgeClass(): string | null
    {
        return  $this->badgeClass;
    }
}
