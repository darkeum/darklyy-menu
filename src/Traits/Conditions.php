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

trait Conditions
{
    protected function resolveCondition(mixed $conditional): mixed
    {
        return is_callable($conditional) ? $conditional() : $conditional;
    }
}
