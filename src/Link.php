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
use Darkeum\Menu\Html\Attributes;
use Darkeum\Menu\Traits\Activatable as ActivatableTrait;
use Darkeum\Menu\Traits\Conditions as ConditionsTrait;
use Darkeum\Menu\Traits\HasHtmlAttributes as HasHtmlAttributesTrait;
use Darkeum\Menu\Traits\HasParentAttributes as HasParentAttributesTrait;
use Darkeum\Menu\Traits\HasTextAttributes as HasAttributesTrait;

class Link implements Item, HasHtmlAttributes, HasParentAttributes, Activatable
{
    use Macroable;

    use ActivatableTrait;
    use HasHtmlAttributesTrait;
    use HasParentAttributesTrait;
    use ConditionsTrait;
    use HasAttributesTrait;

    protected string $prepend = '';

    protected string $append = '';

    protected bool $active = false;

    protected Attributes $htmlAttributes;

    protected Attributes $parentAttributes;

    protected string | null $icon = null;

    protected function __construct(
        protected string | null $url,
        protected string $text,
    ) {
        $this->htmlAttributes = new Attributes();
        $this->parentAttributes = new Attributes();
    }

    public function text(): string
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public static function toUrl(string $path, string $text, mixed $parameters = [], bool | null $secure = null): static
    {
        return static::to(url($path, $parameters, $secure), $text);
    }

    public static function to(string $url, string $text): static
    {
        if (strpos($url, 'javascript:void(0);') || strpos($url, 'javascript:;')) {
            $url = '/' . $url;
        }

        return new static($url, $text);
    }

    public static function toAction(string | array $action, string $text, mixed $parameters = [], bool $absolute = true): static
    {
        if (is_array($action)) {
            $action = implode('@', $action);
        }

        return static::to(action($action, $parameters, $absolute), $text);
    }

    public static function toRoute(string $name, string $text, mixed $parameters = [], bool $absolute = true): static
    {
        return static::to(route($name, $parameters, $absolute), $text);
    }

    public function render(): string
    {
        if (strpos($this->url, 'javascript:void(0);') || strpos($this->url, 'javascript:;')) {
            $this->url = substr($this->url, 1);
        }

        if (filter_var($this->url, FILTER_VALIDATE_URL) && (strpos($this->url, 'javascript:void(0);') || strpos($this->url, 'javascript:;'))) {
            $this->url = parse_url($this->url, PHP_URL_PATH);
            $this->url = substr($this->url, 1);
        }

        $attributes = new Attributes(['href' => $this->url]);
        $attributes->mergeWith($this->htmlAttributes);

        // return $this->prepend . "<a {$attributes}>{$this->text}</a>" . $this->append;
        return $this->prepend . '<a '.$attributes.'><span class="' . $this->icon . '"></span><span class="sidebar-title">'.$this->text.'</span></a>' . $this->append;
    }
}
