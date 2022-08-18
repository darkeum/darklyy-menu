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

use Darkeum\Menu\Html\Attributes;
use Illuminate\Support\Traits\Macroable;
use Darkeum\Menu\Traits\Activatable as ActivatableTrait;
use Darkeum\Menu\Traits\HasParentAttributes as HasParentAttributesTrait;

class Html implements Item, Activatable, HasParentAttributes
{
    use Macroable;
    use ActivatableTrait;
    use HasParentAttributesTrait;

    protected string | null $url = null;

    protected bool $active = false;

    protected Attributes $parentAttributes;

    protected function __construct(protected string $html)
    {
        $this->parentAttributes = new Attributes();
    }

    /**
     * Создает элемент, содержащий кусок необработанного html.
     *
     * @param string $html
     *
     * @return static
     */
    public static function raw(string $html): static
    {
        return new static($html);
    }

    /**
     * Создает пустой элемент.
     *
     * @return static
     */
    public static function empty(): static
    {
        return new static('');
    }

    public function html(): string
    {
        return $this->html;
    }

    public function render(): string
    {
        return $this->html;
    }
}
