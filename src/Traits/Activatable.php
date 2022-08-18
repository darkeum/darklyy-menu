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

use Darkeum\Menu\ActiveUrlChecker;
use Darkeum\Menu\ExactUrlChecker;

trait Activatable
{
    protected bool $exactActive = false;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool | callable $active = true): static
    {
        if (is_callable($active)) {
            $this->active = $active($this);

            return $this;
        }

        $this->active = $active;

        return $this;
    }

    public function setInactive(): static
    {
        $this->active = false;

        return $this;
    }

    public function url(): string | null
    {
        return $this->url;
    }

    public function hasUrl(): bool
    {
        return ! is_null($this->url);
    }

    public function setUrl(string | null $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function determineActiveForUrl(string $url, string $root = '/'): void
    {
        if (! $this->hasUrl()) {
            return;
        }

        ActiveUrlChecker::check($this->url, $url, $root)
            ? $this->setActive()
            : $this->setInactive();

        ExactUrlChecker::check($this->url, $url, $root)
            ? $this->setExactActive()
            : $this->setExactActive(false);
    }

    /**
     * Устанавливает, должен ли текущий Activable быть помечен как точное совпадение URL.
     *
     * @param bool $exactActive
     *
     * @return $this
     */
    public function setExactActive(bool $exactActive = true): static
    {
        $this->exactActive = $exactActive;

        return $this;
    }

    /**
     * Проверяет, помечен ли текущий Activable как точное совпадение URL.
     *
     * @return bool
     */
    public function isExactActive(): bool
    {
        return $this->exactActive;
    }
}
