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

interface HasParentAttributes
{
    /**
     * Возвращает массив атрибутов для применения к родителю. Обычно это означает
     * атрибуты, которые должны быть применены к тегу <li>.
     *
     * @return array
     */
    public function parentAttributes(): array;

    public function setParentAttribute(string $attribute, string $value = ''): static;

    public function setParentAttributes(array $attributes): static;

    public function addParentClass(string $class): static;
}
