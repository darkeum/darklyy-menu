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

interface Activatable
{
    public function setActive(bool | callable $active = true): static;

    public function setInactive(): static;

    public function url(): string | null;

    public function hasUrl(): bool;

    public function setUrl(string | null $url): static;

    public function determineActiveForUrl(string $url, string $root = '/'): void;
}
