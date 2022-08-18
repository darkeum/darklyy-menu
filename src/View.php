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

use Illuminate\Support\Traits\Macroable;
use Darkeum\Menu\Activatable;
use Darkeum\Menu\HasParentAttributes;
use Darkeum\Menu\Html\Attributes;
use Darkeum\Menu\Item;
use Darkeum\Menu\Traits\Activatable as ActivatableTrait;
use Darkeum\Menu\Traits\HasParentAttributes as HasParentAttributesTrait;

class View implements Item, Activatable, HasParentAttributes
{
    use ActivatableTrait;
    use Macroable;
    use HasParentAttributesTrait;

    protected string | null $url = null;

    protected bool $active = false;

    protected Attributes $parentAttributes;

    public function __construct(
        protected string $name,
        protected array $data = [],
    ) {
        $this->parentAttributes = new Attributes();
    }

    public static function create(string $name, array $data = []): static
    {
        $view = new static($name, $data);

        if (array_key_exists('url', $data)) {
            $view->setUrl($data['url']);
        }

        return $view;
    }

    public function render(): string
    {
        return view($this->name)
            ->with($this->data + ['active' => $this->isActive()])
            ->render();
    }
}
