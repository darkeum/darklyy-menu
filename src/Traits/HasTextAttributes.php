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

use Darkeum\Menu\Item;

trait HasTextAttributes
{
    /**
     * Добавляет строку перед при рендеринге.
     *
     * @param string|Item $prepend
     *
     * @return $this
     */
    public function prepend(string | Item $prepend): self
    {
        $this->prepend = $prepend;

        return $this;
    }

    /**
     * Добавляет строку перед при рендеринге, если выполняется определенное условие.
     *
     * @param mixed $condition
     * @param string|Item $prepend
     *
     * @return $this
     */
    public function prependIf(mixed $condition, string | Item $prepend): self
    {
        if ($this->resolveCondition($condition)) {
            return $this->prepend($prepend);
        }

        return $this;
    }

    /**
     * Добавляет строку после при рендеринге,
     *
     * @param string|Item $append
     *
     * @return $this
     */
    public function append(string | Item $append): self
    {
        $this->append = $append;

        return $this;
    }

    /**
     * Добавляет строку после при рендеринге, если выполняется определенное условие.
     *
     * @param bool|callable $condition
     * @param string|Item $append
     *
     * @return $this
     */
    public function appendIf(bool | callable $condition, string | Item $append): self
    {
        if ($this->resolveCondition($condition)) {
            return $this->append($append);
        }

        return $this;
    }

    protected function renderPrepend(): string
    {
        return $this->prepend instanceof Item
            ? $this->prepend->render()
            : $this->prepend;
    }

    protected function renderAppend(): string
    {
        return $this->append instanceof Item
            ? $this->append->render()
            : $this->append;
    }
}
