<?php

/*
* @name        DARKLYY-MENU
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Darkeum\Menu\Helpers;

use ReflectionClass;
use ReflectionFunction;
use ReflectionObject;
use ReflectionParameter;
use Darkeum\Menu\Item;

class Reflection
{   
    public static function firstParameterType(callable | object $callable): string
    {
        $reflection = is_object($callable)
            ? (new ReflectionObject($callable))->getMethod('__invoke')
            : new ReflectionFunction($callable);

        $parameters = $reflection->getParameters();

        $parameterTypes = array_map(function (ReflectionParameter $parameter) {
            $class = $parameter->getType()
                ? new ReflectionClass($parameter->getType()->getName())
                : null;

            return $class?->name;
        }, $parameters);

        return $parameterTypes[0] ?? '';
    }

    public static function itemMatchesType(Item $item, string $type): bool
    {
        if ($type === '') {
            return true;
        }

        return $item instanceof $type;
    }
}
