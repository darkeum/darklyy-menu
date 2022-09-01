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

trait HasText
{
    protected string $text;
    public function text(): string
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }  

    public function getText(): string
    {
        return $this->text;
    }

    

}
