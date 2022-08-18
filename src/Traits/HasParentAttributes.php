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

trait HasParentAttributes
{
    /**
     * Возвращает массив атрибутов для применения к родителю. Обычно это означает
     * атрибуты, которые должны быть применены к тегу <li>.
     *
     * @return array
     */
    public function parentAttributes(): array
    {
        return $this->parentAttributes->toArray();
    }

    public function setParentAttribute(string $attribute, string $value = ''): static
    {
        $this->parentAttributes->setAttribute($attribute, $value);

        return $this;
    }

    public function setParentAttributes(array $attributes): static
    {
        $this->parentAttributes->setAttributes($attributes);

        return $this;
    }

    public function addParentClass(string $class): static
    {
        $this->parentAttributes->addClass($class);

        return $this;
    }
}
