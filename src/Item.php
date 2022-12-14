<?php

namespace Darkeum\Menu;

interface Item
{
    /**
     * Determine whether the item is active or not.
     *
     * @return bool
     */
    public function isActive(): bool;

    /**
     * Render the item in html.
     *
     * @return string
     */
    public function render(): string;
}
