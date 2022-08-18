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

interface HasHtmlAttributes
{
    public function setAttribute(string $attribute, string $value = ''): static;

    public function setAttributes(array $attributes): static;

    public function addClass(string $class): static;
}
