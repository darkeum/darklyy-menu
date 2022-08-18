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

trait HasHtmlAttributes
{
    public function setAttribute(string $attribute, string $value = ''): static
    {
        $this->htmlAttributes->setAttribute($attribute, $value);

        return $this;
    }

    public function setAttributes(array $attributes): static
    {
        $this->htmlAttributes->setAttributes($attributes);

        return $this;
    }

    public function addClass(string $class): static
    {
        $this->htmlAttributes->addClass($class);

        return $this;
    }
}
