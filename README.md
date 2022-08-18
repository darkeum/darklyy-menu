# Генератор HTML меню для Darklyy

[![Latest Version on Packagist](https://img.shields.io/packagist/v/darkeum/darklyy-menu.svg?style=flat-square)](https://packagist.org/packages//darkeum/darklyy-menu)
[![Total Downloads](https://img.shields.io/packagist/dt/darkeum/darklyy-menu.svg?style=flat-square)](https://packagist.org/packages/darkeum/darklyy-menu)

Пакет Darkeum/darklyy-menu предоставляет удобный интерфейс для создания меню любого размера в вашем приложении Darklyy. 

```php
Menu::macro('main', function () {
    return Menu::new()
        ->url('/', 'Главная')
        ->route('feedback', 'Контакты')
        ->action('PageController@about', 'О нас');
        ->setActiveFromRequest();
});
```

```html
<nav class="navigation">
    {!! Menu::main() !!}
</nav>
```

## Installation
Вы можете установить пакет через composer:

``` bash
composer require darkeum/darklyy-menu
```

