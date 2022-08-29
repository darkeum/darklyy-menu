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

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Traits\Macroable;
use Darkeum\Menu\Item;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Darkeum\Menu\Helpers\Reflection;
use Darkeum\Menu\Html\Attributes;
use Darkeum\Menu\Html\Tag;
use Darkeum\Menu\Traits\Conditions as ConditionsTrait;
use Darkeum\Menu\Traits\HasHtmlAttributes as HasHtmlAttributesTrait;
use Darkeum\Menu\Traits\HasParentAttributes as HasParentAttributesTrait;
use Darkeum\Menu\Traits\HasTextAttributes as HasAttributesTrait;
use Traversable;

class Menu implements Htmlable, Item, Countable, HasHtmlAttributes, HasParentAttributes, IteratorAggregate
{
    use HasHtmlAttributesTrait;
    use HasParentAttributesTrait;
    use ConditionsTrait;
    use HasAttributesTrait;
    use Macroable;

    protected array $items = [];

    protected array $filters = [];

    protected string | Item $prepend = '';

    protected string | Item $append = '';

    protected array $wrap = [];

    protected string $activeClass = 'active';

    protected string $exactActiveClass = 'exact-active';

    protected string | null $wrapperTagName = 'ul';

    protected string | null $parentTagName = 'li';

    protected bool $activeClassOnParent = true;

    protected bool $activeClassOnLink = false;

    protected Attributes $htmlAttributes;

    protected Attributes $parentAttributes;

    protected function __construct(Item ...$items)
    {
        $this->items = $items;

        $this->htmlAttributes = new Attributes();
        $this->parentAttributes = new Attributes();
    }

    /**
     * Создание новое меню, предварительно заполненное элементами.
     *
     * @param array $items
     *
     * @return static
     */
    public static function new($items = [], $type = null): static
    {
        return new static(...array_values($items));
    }

    /**
     * Построить новое меню из массива. Обратный вызов получает экземпляр меню 
     * элемент массива в качестве второго параметра и элемент
     * ключ как третий.
     *
     * @param array|\Iterator $items
     * @param callable $callback
     * @param \Darkeum\Menu\Menu|null $initial
     *
     * @return static
     */
    public static function build(array | \Iterator $items, callable $callback, self | null $initial = null): static
    {
        return ($initial ?: static::new())->fill($items, $callback);
    }

    /**
     * Заполнить меню из массива. Обратный вызов получает экземпляр меню 
     * элемент массива в качестве второго параметра и элемент
     * ключ как третий.
     *
     * @param array|\Iterator $items
     * @param callable $callback
     *
     * @return static
     */
    public function fill(array | \Iterator $items, callable $callback): self
    {
        $menu = $this;

        foreach ($items as $key => $item) {
            $menu = $callback($menu, $item, $key) ?: $menu;
        }

        return $menu;
    }

    /**
     * Добавить пункт в меню. Это также применяет к элементу все зарегистрированные фильтры.
     *
     * @param \Darkeum\Menu\Item $item
     *
     * @return $this
     */
    public function add(Item $item): self
    {
        foreach ($this->filters as $filter) {
            $this->applyFilter($filter, $item);
        }

        $this->items[] = $item;

        return $this;
    }

    /**
     * Добавить пункт в меню, если выполняется (нестрогое) условие.
     *
     * @param bool|callable $condition
     * @param \Darkeum\Menu\Item $item
     *
     * @return $this
     */
    public function addIf(bool | callable $condition, Item $item): self
    {
        if ($this->resolveCondition($condition)) {
            $this->add($item);
        }

        return $this;
    }

    /**
     * Функция ярлыка для добавления простой ссылки в меню.
     *
     * @param string $url
     * @param string $text
     *
     * @return $this
     */
    public function link(string $url, string $text): self
    {
        return $this->add(Link::to($url, $text));
    }

    /**
     * Функция быстрого доступа для добавления пустого пункта в меню.
     *
     * @return $this
     */
    public function empty(): self
    {
        return $this->add(Html::empty());
    }

    /**
     * Добавьте ссылку в меню, если выполняется (нестрогое) условие.
     *
     * @param bool|callable $condition
     * @param string $url
     * @param string $text
     *
     * @return $this
     */
    public function linkIf(bool | callable $condition, string $url, string $text): self
    {
        if ($this->resolveCondition($condition)) {
            $this->link($url, $text);
        }

        return $this;
    }

    /**
     * Функция ярлыка для добавления необработанного html в меню.
     *
     * @param string $html
     * @param array $parentAttributes
     *
     * @return $this
     */
    public function html(string $html, array $parentAttributes = []): self
    {
        return $this->add(Html::raw($html)->setParentAttributes($parentAttributes));
    }

    /**
     * Добавьте фрагмент html, если выполняется (нестрогое) условие.
     *
     * @param bool|callable $condition
     * @param string $html
     * @param array $parentAttributes
     *
     * @return $this
     */
    public function htmlIf(bool | callable $condition, string $html, array $parentAttributes = []): self
    {
        if ($this->resolveCondition($condition)) {
            $this->html($html, $parentAttributes);
        }

        return $this;
    }

    public function submenu(callable | self | Item | string $header, callable | self | null $menu = null): self
    {
        [$header, $menu] = $this->parseSubmenuArgs(func_get_args());

        $menu = $this->createSubmenuMenu($menu);

        return $this->add($menu->prependIf($header, $header));
    }

    public function submenuIf(bool $condition, callable | self | Item | string $header, callable | self | null $menu = null): self
    {
        if ($condition) {
            $this->submenu($header, $menu);
        }

        return $this;
    }

    protected function parseSubmenuArgs($args): array
    {
        if (count($args) === 1) {
            return ['', $args[0]];
        }

        return [$args[0], $args[1]];
    }

    protected function createSubmenuMenu(self | callable $menu): self
    {
        if (is_callable($menu)) {
            $transformer = $menu;
            $menu = $this->blueprint();
            $transformer($menu);
        }

        return $menu;
    }

    protected function createSubmenuHeader(Item | string $header): string
    {
        if ($header instanceof Item) {
            $header = $header->render();
        }

        return $header;
    }

      public function each(callable $callable): self
    {
        $type = Reflection::firstParameterType($callable);

        foreach ($this->items as $item) {
            if (!Reflection::itemMatchesType($item, $type)) {
                continue;
            }

            $callable($item);
        }

        return $this;
    }

    public function registerFilter(callable $callable): self
    {
        $this->filters[] = $callable;

        return $this;
    }

    protected function applyFilter(callable $filter, Item $item)
    {
        $type = Reflection::firstParameterType($filter);

        if (!Reflection::itemMatchesType($item, $type)) {
            return;
        }

        $filter($item);
    }

    public function applyToAll(callable $callable): self
    {
        $this->each($callable);
        $this->registerFilter($callable);

        return $this;
    }

    public function wrap(string $element, array $attributes = []): self
    {
        $this->wrap = [$element, $attributes];

        return $this;
    }

    public function isActive(): bool
    {
        foreach ($this->items as $item) {
            if ($item->isActive()) {
                return true;
            }
        }

        if ($this->prepend && $this->prepend instanceof Item && $this->prepend->isActive()) {
            return true;
        }

        return false;
    }

    public function isExactActive(): bool
    {
        if (!$this->prepend) {
            return false;
        }

        if (!method_exists($this->prepend, 'isExactActive')) {
            return false;
        }

        return $this->prepend->isExactActive();
    }

    public function setActive(callable | string $urlOrCallable, string $root = '/'): self
    {
        if (is_string($urlOrCallable)) {
            return $this->setActiveFromUrl($urlOrCallable, $root);
        }

        return $this->setActiveFromCallable($urlOrCallable);
    }


    public function setExactActiveClass(string $class)
    {
        $this->exactActiveClass = $class;

        return $this;
    }

    public function setActiveFromUrl(string $url, string $root = '/'): self
    {
        $this->applyToAll(function (Menu $menu) use ($url, $root) {
            $menu->setActiveFromUrl($url, $root);
        });

        if ($this->prepend instanceof Activatable) {
            $this->prepend->determineActiveForUrl($url, $root);
        }

        $this->applyToAll(function (Activatable $item) use ($url, $root) {
            $item->determineActiveForUrl($url, $root);
        });

        return $this;
    }

    public function setActiveFromCallable(callable $callable): self
    {
        $this->applyToAll(function (Menu $menu) use ($callable) {
            $menu->setActiveFromCallable($callable);
        });

        $type = Reflection::firstParameterType($callable);

        $this->applyToAll(function (Activatable $item) use ($callable, $type) {

   
            if (!Reflection::itemMatchesType($item, $type)) {
                return;
            }

            if ($callable($item)) {
                $item->setActive();
                $item->setExactActive();
            }
        });

        return $this;
    }

    public function setActiveClass(string $class): self
    {
        $this->activeClass = $class;

        return $this;
    }

    public function addItemClass(string $class): self
    {
        $this->applyToAll(function (HasHtmlAttributes $link) use ($class) {
            $link->addClass($class);
        });

        return $this;
    }

    public function setItemAttribute(string $attribute, string $value = ''): self
    {
        $this->applyToAll(function (HasHtmlAttributes $link) use ($attribute, $value) {
            $link->setAttribute($attribute, $value);
        });

        return $this;
    }

    public function addItemParentClass(string $class): self
    {
        $this->applyToAll(function (HasParentAttributes $item) use ($class) {
            $item->addParentClass($class);
        });

        return $this;
    }

    public function setItemParentAttribute(string $attribute, string $value = ''): self
    {
        $this->applyToAll(function (HasParentAttributes $item) use ($attribute, $value) {
            $item->setParentAttribute($attribute, $value);
        });

        return $this;
    }

    public function setWrapperTag(string | null $wrapperTagName = null): self
    {
        $this->wrapperTagName = $wrapperTagName;

        return $this;
    }

    public function withoutWrapperTag(): self
    {
        $this->wrapperTagName = null;

        return $this;
    }

    public function setParentTag(string | null $parentTagName = null): self
    {
        $this->parentTagName = $parentTagName;

        return $this;
    }

    public function withoutParentTag(): self
    {
        $this->parentTagName = null;

        return $this;
    }

    public function setActiveClassOnLink(bool $activeClassOnLink = true): self
    {
        $this->activeClassOnLink = $activeClassOnLink;

        return $this;
    }

    public function setActiveClassOnParent(bool $activeClassOnParent = true): self
    {
        $this->activeClassOnParent = $activeClassOnParent;

        return $this;
    }

    public function if(bool $condition, callable $callable)
    {
        return $condition ? $callable($this) : $this;
    }

    public function blueprint(): static
    {
        $clone = new static();

        $clone->filters = $this->filters;
        $clone->activeClass = $this->activeClass;

        return $clone;
    }

    public function render(): string
    {
        $tag = $this->wrapperTagName
            ? new Tag($this->wrapperTagName, $this->htmlAttributes)
            : null;

        $contents = array_map([$this, 'renderItem'], $this->items);

        $wrappedContents = $tag ? $tag->withContents($contents) : implode('', $contents);

        if ($this->prepend instanceof Item && $this->prepend->isActive()) {
            $this->prepend = $this->renderActiveClassOnLink($this->prepend);
        }

        $menu = $this->renderPrepend() . $wrappedContents . $this->renderAppend();

        if (!empty($this->wrap)) {
            return Tag::make($this->wrap[0], new Attributes($this->wrap[1]))->withContents($menu);
        }

        return $menu;
    }

    protected function renderItem(Item $item): string
    {
        $attributes = new Attributes();

        if (method_exists($item, 'beforeRender')) {
            $item->beforeRender();
        }

        if (method_exists($item, 'willRender') && $item->willRender() === false) {
            return '';
        }

        if ($item->isActive()) {
            if ($this->activeClassOnParent) {
                $attributes->addClass($this->activeClass);

                if ($item->isExactActive()) {
                    $attributes->addClass($this->exactActiveClass);
                }
            }

            $item = $this->renderActiveClassOnLink($item);
        }

        if ($item instanceof HasParentAttributes) {
            $attributes->setAttributes($item->parentAttributes());
        }

        if (!$this->parentTagName) {
            return $item->render();
        }

        return Tag::make($this->parentTagName, $attributes)->withContents($item->render());
    }

    protected function renderActiveClassOnLink(Item $item): Item
    {
        if ($this->activeClassOnLink && $item instanceof HasHtmlAttributes && !$item instanceof Menu) {
            $item->addClass($this->activeClass);

            if ($item->isExactActive()) {
                $item->addClass($this->exactActiveClass);
            }
        }

        return $item;
    }

    /**
     * Количество позиций в меню.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function setActiveFromRequest(string $requestRoot = '/'): self
    {
        return $this->setActive(app('request')->url(), $requestRoot);
    }

    public function url(string $path, string $text, mixed $parameters = [], bool | null $secure = null): self
    {
        return $this->add(Link::toUrl($path, $text, $parameters, $secure));
    }

    public function action(string | array $action, string $text, mixed $parameters = [], bool $absolute = true): self
    {
        return $this->add(Link::toAction($action, $text, $parameters, $absolute));
    }

    public function route(string $name, string $text, mixed $parameters = [], bool $absolute = true): self
    {
        return $this->add(Link::toRoute($name, $text, $parameters, $absolute));
    }

    public function view(string $name, array $data = []): self
    {
        return $this->add(View::create($name, $data));
    }

    public function urlIf(bool $condition, string $path, string $text, array $parameters = [], bool | null $secure = null): self
    {
        return $this->addIf($condition, Link::toUrl($path, $text, $parameters, $secure));
    }

    public function actionIf(bool $condition, string | array $action, string $text, array $parameters = [], bool $absolute = true): self
    {
        return $this->addIf($condition, Link::toAction($action, $text, $parameters, $absolute));
    }

    public function routeIf(bool $condition, string $name, string $text, array $parameters = [], bool $absolute = true): self
    {
        return $this->addIf($condition, Link::toRoute($name, $text, $parameters, $absolute));
    }

    public function viewIf($condition, string $name, array | null $data = null): self
    {
        return $this->addIf($condition, View::create($name, $data));
    }

    public function addIfCan(string | array $authorization, Item $item): self
    {
        $abilityArguments = is_array($authorization) ? $authorization : [$authorization];
        $ability = array_shift($abilityArguments);

        return $this->addIf(app(Gate::class)->allows($ability, $abilityArguments), $item);
    }

    public function linkIfCan(string | array $authorization, string $url, string $text): self
    {
        return $this->addIfCan($authorization, Link::to($url, $text));
    }

    public function htmlIfCan(string | array $authorization, string $html): Menu
    {
        return $this->addIfCan($authorization, Html::raw($html));
    }

    public function submenuIfCan(string | array $authorization, callable | Menu | Item $header, callable | Menu | null $menu = null): self
    {
        [$authorization, $header, $menu] = $this->parseSubmenuIfCanArgs(...func_get_args());

        $menu = $this->createSubmenuMenu($menu);
        $header = $this->createSubmenuHeader($header);

        return $this->addIfCan($authorization, $menu->prependIf($header, $header));
    }

    protected function parseSubmenuIfCanArgs($authorization, ...$args): array
    {
        return array_merge([$authorization], $this->parseSubmenuArgs($args));
    }

    public function urlIfCan(string | array $authorization, string $path, string $text, array $parameters = [], bool | null $secure = null): self
    {
        return $this->addIfCan($authorization, Link::toUrl($path, $text, $parameters, $secure));
    }

    public function actionIfCan(string | array $authorization, string | array $action, string $text, array $parameters = [], bool $absolute = true): self
    {
        return $this->addIfCan($authorization, Link::toAction($action, $text, $parameters, $absolute));
    }

    public function routeIfCan(string | array $authorization, string $name, string $text, array $parameters = [], bool $absolute = true): self
    {
        return $this->addIfCan($authorization, Link::toRoute($name, $text, $parameters, $absolute));
    }

    public function viewIfCan(string | array $authorization, string $name, array | null $data = null): self
    {
        return $this->addIfCan($authorization, View::create($name, $data));
    }

    public function toHtml(): string
    {
        return $this->render();
    }
}
